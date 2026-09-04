<?php

class UserService
{
    public function __construct(private PDO $pdo)
    {
    }

    public function isAdmin(int $userId): bool
    {
        $stmt = $this->pdo->prepare('SELECT type FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user !== false && ($user['type'] ?? '') === 'ADMIN';
    }

    public function getUser(int $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, email, type FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $userId]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function getDashboardStats(): array
    {
        $stats = [];

        $stats['users'] = (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $stats['categories'] = (int) $this->pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
        $stats['products'] = (int) $this->pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
        $stats['orders'] = (int) $this->pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();

        return $stats;
    }
}
