<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class ProductService {
    public static function getProducts(string $search = '', string $sort = '', bool $includeInactive = false): array {
        $pdo = Database::connect();
        $sql = 'SELECT p.*, COALESCE(i.quantity, 0) AS quantity FROM products p LEFT JOIN inventory i ON p.id = i.product_id';
        $clauses = [];
        $params = [];
        if (!$includeInactive) {
            $clauses[] = 'p.is_active = 1';
        }
        if ($search !== '') {
            $clauses[] = '(p.name LIKE ? OR p.description LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($clauses) {
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }
        switch ($sort) {
            case 'price_asc':
                $sql .= ' ORDER BY p.price ASC';
                break;
            case 'price_desc':
                $sql .= ' ORDER BY p.price DESC';
                break;
            case 'avail_asc':
                $sql .= ' ORDER BY quantity ASC';
                break;
            case 'avail_desc':
                $sql .= ' ORDER BY quantity DESC';
                break;
            default:
                $sql .= ' ORDER BY p.created_at DESC';
                break;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getProductById(int $productId): ?array {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT p.*, COALESCE(i.quantity, 0) AS quantity FROM products p LEFT JOIN inventory i ON p.id = i.product_id WHERE p.id = ?');
        $stmt->execute([$productId]);
        return $stmt->fetch() ?: null;
    }

    public static function deleteProduct(int $id): bool {
        $pdo = Database::connect();
        // Remove from active carts first (not historical records, safe to purge)
        $pdo->prepare('DELETE FROM cart_items WHERE product_id = ?')->execute([$id]);
        // Delete the product; inventory row cascades automatically via FK
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public static function saveProduct(array $data): bool {
        $pdo = Database::connect();
        $imageUrl = trim($data['image_url'] ?? '') ?: DEFAULT_IMAGE;
        $isActive = !empty($data['is_active']) ? 1 : 0;
        if (!empty($data['id'])) {
            $stmt = $pdo->prepare('UPDATE products SET name = ?, description = ?, price = ?, image_url = ?, is_active = ? WHERE id = ?');
            $result = $stmt->execute([
                $data['name'],
                $data['description'],
                $data['price'],
                $imageUrl,
                $isActive,
                $data['id'],
            ]);
            if ($result) {
                $inventoryStmt = $pdo->prepare('INSERT INTO inventory (product_id, quantity) VALUES (?, ?) ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)');
                $inventoryStmt->execute([$data['id'], $data['quantity']]);
            }
            return $result;
        }
        $stmt = $pdo->prepare('INSERT INTO products (name, description, price, image_url, is_active) VALUES (?, ?, ?, ?, ?)');
        $result = $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $imageUrl,
            $isActive,
        ]);
        if ($result) {
            $productId = (int)$pdo->lastInsertId();
            $inventoryStmt = $pdo->prepare('INSERT INTO inventory (product_id, quantity) VALUES (?, ?)');
            $inventoryStmt->execute([$productId, $data['quantity']]);
            return true;
        }
        return false;
    }
}
