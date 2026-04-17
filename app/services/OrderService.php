<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/CartService.php';

class OrderService {
    public static function getAllOrders(string $sort = ''): array {
        $pdo = Database::connect();
        $sql = 'SELECT o.*, u.first_name, u.last_name, u.email FROM orders o JOIN users u ON o.user_id = u.id';
        switch ($sort) {
            case 'customer_asc':
                $sql .= ' ORDER BY u.last_name ASC, u.first_name ASC';
                break;
            case 'customer_desc':
                $sql .= ' ORDER BY u.last_name DESC, u.first_name DESC';
                break;
            case 'total_asc':
                $sql .= ' ORDER BY o.total ASC';
                break;
            case 'total_desc':
                $sql .= ' ORDER BY o.total DESC';
                break;
            default:
                $sql .= ' ORDER BY o.created_at DESC';
                break;
        }
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    public static function getOrdersForUser(int $userId): array {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function getOrderItems(int $orderId): array {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'SELECT oi.*, p.name, p.image_url
             FROM order_items oi
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?'
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public static function getTotalRevenue(): float {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled'");
        return (float)$stmt->fetchColumn();
    }

    public static function getRecentOrders(int $limit = 5): array {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'SELECT o.*, u.first_name, u.last_name
             FROM orders o
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function updateOrderStatus(int $orderId, string $status): bool {
        $allowed = ['pending', 'paid', 'fulfilled', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }
        $pdo = Database::connect();
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $orderId]);
    }

    public static function placeOrder(int $userId): ?int {
        $pdo = Database::connect();
        $cart = CartService::getActiveCart($userId);
        $items = CartService::getCartItems($cart['id']);
        if (empty($items)) {
            return null;
        }
        $coupon = CartService::getCartCoupon();
        $totals = CartService::calculateCartTotals($items, $coupon);
        try {
            $pdo->beginTransaction();
            // Lock inventory rows first to prevent race conditions with concurrent orders
            $lockStmt = $pdo->prepare('SELECT quantity FROM inventory WHERE product_id = ? FOR UPDATE');
            foreach ($items as $item) {
                $lockStmt->execute([$item['product_id']]);
                $stock = (int)$lockStmt->fetchColumn();
                if ($item['quantity'] > $stock) {
                    throw new RuntimeException('Not enough stock for "' . $item['name'] . '".');
                }
            }
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, subtotal, tax, discount, total) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$userId, $totals['subtotal'], $totals['tax'], $totals['discount'], $totals['total']]);
            $orderId = (int)$pdo->lastInsertId();
            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
            $inventoryStmt = $pdo->prepare('UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?');
            foreach ($items as $item) {
                $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
                $inventoryStmt->execute([$item['quantity'], $item['product_id']]);
            }
            $stmt = $pdo->prepare('UPDATE carts SET status = "converted" WHERE id = ?');
            $stmt->execute([$cart['id']]);
            $pdo->commit();
            CartService::clearCartCoupon();
            return $orderId;
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }
}
