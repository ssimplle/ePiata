<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$userId = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare(
    'SELECT o.id, o.created_at, o.order_status, SUM(op.quantity * op.product_price) AS total
     FROM orders o
     LEFT JOIN order_products op ON op.order_id = o.id
     WHERE o.user_id = :user_id
     GROUP BY o.id, o.created_at, o.order_status
     ORDER BY o.created_at DESC'
);
$stmt->execute([':user_id' => $userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="content-card">
    <p class="eyebrow">Comenzi</p>
    <h1>Istoricul comenzilor</h1>
    <p>Urmărești starea fiecărei comenzi și valoarea totală plătită.</p>
</section>

<?php if (empty($orders)): ?>
    <section class="content-card">
        <p>Nu ai plasat nicio comandă încă.</p>
    </section>
<?php else: ?>
    <section class="stats-grid">
        <?php foreach ($orders as $order): ?>
            <article class="stat-card">
                <h2>Comanda #<?= (int) $order['id'] ?></h2>
                <p>Status: <?= htmlspecialchars($order['order_status'], ENT_QUOTES, 'UTF-8') ?></p>
                <p>Data: <?= htmlspecialchars($order['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
                <p>Total: <?= number_format((float) ($order['total'] ?? 0), 2, ',', '.') ?> MDL</p>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
