<?php

class CategoryService
{
    public function __construct(private PDO $pdo)
    {
    }

    public function read(): array
    {
        $stmt = $this->pdo->query('SELECT id, name FROM categories ORDER BY name ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $name): bool
    {
        $trimmedName = trim($name);
        if ($trimmedName === '' || strlen($trimmedName) > 30) {
            return false;
        }

        $stmt = $this->pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
        return $stmt->execute([':name' => $trimmedName]);
    }

    public function update(int $id, string $name): bool
    {
        $trimmedName = trim($name);
        if ($trimmedName === '' || strlen($trimmedName) > 30) {
            return false;
        }

        $stmt = $this->pdo->prepare('UPDATE categories SET name = :name WHERE id = :id');
        return $stmt->execute([':name' => $trimmedName, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
