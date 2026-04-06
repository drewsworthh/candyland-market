<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ProductService.php';

class CartService {
    public static function getActiveCart(int $userId): array {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM carts WHERE user_id = ? AND status = "active" ORDER BY created_at DESC LIMIT 1');
        $stmt->execute([$userId]);
        $cart = $stmt->fetch();
        if ($cart) {
            return $cart;
        }
        $stmt = $pdo->prepare('INSERT INTO carts (user_id) VALUES (?)');
        $stmt->execute([$userId]);
        return ['id' => (int)$pdo->lastInsertId(), 'user_id' => $userId, 'status' => 'active'];
    }

    public static function getCartItems(int $cartId): array {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'SELECT ci.id, ci.quantity, p.id AS product_id, p.name, p.description, p.price, p.image_url, p.is_active, COALESCE(i.quantity, 0) AS stock
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             LEFT JOIN inventory i ON p.id = i.product_id
             WHERE ci.cart_id = ?'
        );
        $stmt->execute([$cartId]);
        return $stmt->fetchAll();
    }

    public static function getProductById(int $productId): ?array {
        return ProductService::getProductById($productId);
    }

    public static function addToCart(int $userId, int $productId, int $quantity): bool {
        $product = self::getProductById($productId);
        if (!$product || !$product['is_active'] || $product['quantity'] <= 0) {
            return false;
        }
        $quantity = max(1, $quantity);
        $cart = self::getActiveCart($userId);
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?');
        $stmt->execute([$cart['id'], $productId]);
        $item = $stmt->fetch();
        if ($item) {
            $newQuantity = min($product['quantity'], $item['quantity'] + $quantity);
            if ($newQuantity <= 0) {
                return false;
            }
            $stmt = $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
            $stmt->execute([$newQuantity, $item['id']]);
        } else {
            $newQuantity = min($product['quantity'], $quantity);
            if ($newQuantity <= 0) {
                return false;
            }
            $stmt = $pdo->prepare('INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)');
            $stmt->execute([$cart['id'], $productId, $newQuantity]);
        }
        return true;
    }

    public static function updateCartItem(int $cartId, int $itemId, int $quantity): bool {
        $pdo = Database::connect();
        if ($quantity <= 0) {
            $stmt = $pdo->prepare('DELETE FROM cart_items WHERE id = ? AND cart_id = ?');
            return $stmt->execute([$itemId, $cartId]);
        }
        $stmt = $pdo->prepare('SELECT product_id FROM cart_items WHERE id = ? AND cart_id = ?');
        $stmt->execute([$itemId, $cartId]);
        $item = $stmt->fetch();
        if (!$item) {
            return false;
        }
        $product = self::getProductById((int)$item['product_id']);
        $quantity = min($quantity, $product['quantity']);
        $stmt = $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id = ?');
        return $stmt->execute([$quantity, $itemId, $cartId]);
    }

    public static function getCartItemCount(int $userId): int {
        $pdo = Database::connect();
        $cart = self::getActiveCart($userId);
        $stmt = $pdo->prepare('SELECT SUM(quantity) AS total FROM cart_items WHERE cart_id = ?');
        $stmt->execute([$cart['id']]);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    public static function calculateCartTotals(array $items, ?array $coupon = null): array {
        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += (float)$item['price'] * (int)$item['quantity'];
        }
        $discount = 0.0;
        if ($coupon) {
            if ($coupon['discount_type'] === 'percent') {
                $discount = round($subtotal * ((float)$coupon['discount_value'] / 100), 2);
            } else {
                $discount = min($subtotal, (float)$coupon['discount_value']);
            }
        }
        $taxable = max(0, $subtotal - $discount);
        $tax = round($taxable * TAX_RATE, 2);
        $total = round($taxable + $tax, 2);
        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
        ];
    }

    public static function setCartCoupon(?array $coupon): void {
        if ($coupon) {
            $_SESSION['cart_coupon'] = $coupon;
        } else {
            unset($_SESSION['cart_coupon']);
        }
    }

    public static function getCartCoupon(): ?array {
        return $_SESSION['cart_coupon'] ?? null;
    }

    public static function clearCartCoupon(): void {
        unset($_SESSION['cart_coupon']);
    }
}
