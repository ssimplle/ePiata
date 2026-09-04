<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $productId = (int) ($_POST['product_id'] ?? 0);
    if ($productId > 0) {
        $stmt = $pdo->prepare('DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id');
        $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
    }
    header('Location: index.php?page=cart');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

    if ($productId > 0) {
        $stmt = $pdo->prepare('UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id');
        $stmt->execute([':quantity' => $quantity, ':user_id' => $userId, ':product_id' => $productId]);
    }
    header('Location: index.php?page=cart');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT c.id, c.product_id, c.quantity, p.name, p.price, COALESCE(pi.file_name, "placeholder.jpg") AS file_name
     FROM cart c
     INNER JOIN products p ON p.id = c.product_id
     LEFT JOIN product_image pi ON pi.product_id = p.id AND pi.image_order = 1
     WHERE c.user_id = :user_id
     ORDER BY c.id DESC'
);
$stmt->execute([':user_id' => $userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cartItems as $item) {
    $total += (float) $item['price'] * (int) $item['quantity'];
}
?>

<section class="content-card">
    <p class="eyebrow">Cos de cumpărături</p>
    <h1>Produsele din coș</h1>
    <p>Verifici cantitatea, ajustezi produsele și pregătești plată pentru comandă.</p>
</section>

<?php if (empty($cartItems)): ?>
    <section class="content-card">
        <p>Coșul este gol pentru moment.</p>
    </section>
<?php else: ?>
    <section class="product-grid">
        <?php foreach ($cartItems as $item): ?>
            <article class="product-card">
                <span class="badge">În coș</span>
                <h3><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p>Preț unitar: <?= number_format((float) $item['price'], 2, ',', '.') ?> MDL</p>
                <div class="product-footer">
                    <form method="POST" style="display:flex; gap:0.5rem; align-items:center; margin:0;">
                        <input type="hidden" name="update_quantity" value="1">
                        <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                        <input type="number" name="quantity" min="1" value="<?= (int) $item['quantity'] ?>" style="width:70px; padding:0.45rem; border-radius:8px; border:1px solid rgba(31,47,41,0.2);">
                        <button class="btn btn-mini" type="submit">Actualizează</button>
                    </form>
                </div>
                <div class="product-footer" style="margin-top:0.8rem;">
                    <strong>Total: <?= number_format((float) $item['price'] * (int) $item['quantity'], 2, ',', '.') ?> MDL</strong>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="remove_item" value="1">
                        <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                        <button class="btn btn-mini" type="submit">Șterge</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="content-card" style="margin-top:1rem;">
        <h2>Total comandă</h2>
        <p><strong><?= number_format((float) $total, 2, ',', '.') ?> MDL</strong></p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="index.php?page=payment">Mergi la plată</a>
        </div>
    </section>
<?php endif; ?>
