<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_favorite'])) {
    $productId = (int) ($_POST['product_id'] ?? 0);
    if ($productId > 0) {
        $stmt = $pdo->prepare('DELETE FROM favorites WHERE user_id = :user_id AND product_id = :product_id');
        $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
    }
    header('Location: index.php?page=favorites');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT p.id, p.name, p.price, p.description, COALESCE(pi.file_name, "placeholder.jpg") AS file_name
     FROM favorites f
     INNER JOIN products p ON p.id = f.product_id
     LEFT JOIN product_image pi ON pi.product_id = p.id AND pi.image_order = 1
     WHERE f.user_id = :user_id
     ORDER BY f.id DESC'
);
$stmt->execute([':user_id' => $userId]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="content-card">
    <p class="eyebrow">Favorite</p>
    <h1>Produsele tale salvate</h1>
    <p>Accesezi rapid articolele preferate si le poti elimina oricand din listă.</p>
</section>

<?php if (empty($favorites)): ?>
    <section class="content-card">
        <p>Nu ai produselor favorite pentru moment.</p>
    </section>
<?php else: ?>
    <section class="product-grid">
        <?php foreach ($favorites as $product): ?>
            <article class="product-card">
                <span class="badge">Favorite</span>
                <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="product-footer">
                    <strong><?= number_format((float) $product['price'], 2, ',', '.') ?> MDL</strong>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="remove_favorite" value="1">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <button class="btn btn-mini" type="submit">Elimină</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
