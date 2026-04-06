<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class DiscountService {
    public static function getDiscountCodes(): array {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM discount_codes ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public static function getDiscountByCode(string $code): ?array {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM discount_codes WHERE UPPER(code) = UPPER(?) AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW())');
        $stmt->execute([$code]);
        return $stmt->fetch() ?: null;
    }

    public static function toggleDiscountCode(int $id): bool {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('UPDATE discount_codes SET is_active = NOT is_active WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function deleteDiscountCode(int $id): bool {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('DELETE FROM discount_codes WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function addDiscountCode(array $data): bool {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('INSERT INTO discount_codes (code, discount_type, discount_value, is_active, expires_at) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([
            strtoupper($data['code']),
            $data['discount_type'],
            $data['discount_value'],
            !empty($data['is_active']) ? 1 : 0,
            $data['expires_at'] ?: null,
        ]);
    }
}
