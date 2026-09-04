<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        http_response_code(419);
        echo 'Sesizare invalidă. Reîncarcă pagina.';
        exit;
    }

    $method = $_POST['payment_method'] ?? 'CASH';
    $stmt = $pdo->prepare('SELECT p.id, p.name, c.quantity, p.price FROM cart c INNER JOIN products p ON p.id = c.product_id WHERE c.user_id = :user_id');
    $stmt->execute([':user_id' => $userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        header('Location: index.php?page=cart');
        exit;
    }

    $pdo->beginTransaction();

    try {
        $orderStmt = $pdo->prepare('INSERT INTO orders (order_status, user_id) VALUES ("IN PROCESS", :user_id)');
        $orderStmt->execute([':user_id' => $userId]);
        $orderId = (int) $pdo->lastInsertId();

        $paymentStmt = $pdo->prepare('INSERT INTO payments (payment_method, payment_status, order_id) VALUES (:method, "PENDING", :order_id)');
        $paymentStmt->execute([':method' => $method, ':order_id' => $orderId]);

        foreach ($items as $item) {
            $orderProductStmt = $pdo->prepare(
                'INSERT INTO order_products (product_price, quantity, order_id, product_id) VALUES (:price, :quantity, :order_id, :product_id)'
            );
            $orderProductStmt->execute([
                ':price' => (float) $item['price'],
                ':quantity' => (int) $item['quantity'],
                ':order_id' => $orderId,
                ':product_id' => (int) $item['id']
            ]);
        }

        $deleteCartStmt = $pdo->prepare('DELETE FROM cart WHERE user_id = :user_id');
        $deleteCartStmt->execute([':user_id' => $userId]);

        $pdo->commit();
        header('Location: index.php?page=orders');
        exit;
    } catch (Throwable $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo 'A apărut o eroare la plasarea comenzii.';
        error_log($e->getMessage());
        exit;
    }
}
?>

<section class="content-card">
    <p class="eyebrow">Plată</p>
    <h1>Finalizează comanda</h1>
    <p>Selectează metoda de plată și confirmă plasarea comenzii.</p>
</section>

<section class="auth-card" style="margin-top:1rem;">
    <form method="POST" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

        <label for="payment_method">Metodă de plată</label>
        <select id="payment_method" name="payment_method" required>
            <option value="CARD">Card</option>
            <option value="CASH">Cash</option>
        </select>

        <button class="btn btn-primary btn-block" type="submit">Confirmă plata</button>
    </form>
</section>
