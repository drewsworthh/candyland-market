<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class UserService {
    public static function getUserByEmail(string $email): ?array {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function getUserById(int $id): ?array {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function createUser(array $data): int {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password_hash'],
            $data['role'],
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function updateUserRecord(int $userId, array $data): bool {
        $pdo = Database::connect();
        $fields = [];
        $values = [];
        foreach (['first_name', 'last_name', 'email', 'role', 'password_hash'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        if (empty($fields)) {
            return false;
        }
        $values[] = $userId;
        $stmt = $pdo->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?');
        return $stmt->execute($values);
    }

    public static function getUsers(): array {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }
}
