<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

session_set_cookie_params([
    'lifetime' => 1800,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

require_once "../includes/db.php";
require_once "../includes/mail.php";
require_once "../includes/services/AuthService.php";
require_once "../includes/services/UserService.php";
require_once "../includes/services/CategoryService.php";

$authService = new AuthService($pdo);
$userService = new UserService($pdo);
$categoryService = new CategoryService($pdo);

$allowedPages = [
    "home",
    "about",
    "contacts",
    "register",
    "login",
    "logout",
    "dashboard",
    "dashboard-stats",
    "categories",
    "products",
    "cart",
    "favorites",
    "orders",
    "payment"
];

$page = $_GET["page"] ?? "home";

if (!in_array($page, $allowedPages)) {
    http_response_code(404);
    echo "Pagina nu a fost gasita.";
    exit;
}

$currentUserId = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!$currentUserId) {
        header('Location: index.php?page=login');
        exit;
    }

    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

    if ($productId > 0) {
        $existingStmt = $pdo->prepare('SELECT id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id LIMIT 1');
        $existingStmt->execute([':user_id' => $currentUserId, ':product_id' => $productId]);
        $existing = $existingStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $updateStmt = $pdo->prepare('UPDATE cart SET quantity = quantity + :quantity WHERE id = :id');
            $updateStmt->execute([':quantity' => $quantity, ':id' => (int) $existing['id']]);
        } else {
            $insertStmt = $pdo->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)');
            $insertStmt->execute([':user_id' => $currentUserId, ':product_id' => $productId, ':quantity' => $quantity]);
        }
    }

    header('Location: index.php?page=cart');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$protectedPages = ['dashboard', 'dashboard-stats', 'categories', 'cart', 'favorites', 'orders', 'payment', 'logout'];
if (in_array($page, $protectedPages) && !$currentUserId) {
    header("Location: index.php?page=login");
    exit;
}

if (in_array($page, ['login', 'register']) && $currentUserId) {
    header("Location: index.php?page=dashboard");
    exit;
}

if ($page === 'dashboard-stats' && $currentUserId && !$userService->isAdmin($currentUserId)) {
    http_response_code(403);
    echo 'Acces interzis.';
    exit;
}

if (($page === 'categories' || $page === 'products') && $currentUserId && !$userService->isAdmin($currentUserId)) {
    http_response_code(403);
    echo 'Acces interzis.';
    exit;
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ePiata</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-background"></div>
    <header class="site-header">
        <a class="brand" href="index.php?page=home">ePiata</a>
        <nav class="main-nav">
            <a href="index.php?page=home" class="<?= $page === 'home' ? 'is-active' : '' ?>">Acasa</a>
            <a href="index.php?page=about" class="<?= $page === 'about' ? 'is-active' : '' ?>">Despre</a>
            <a href="index.php?page=contacts" class="<?= $page === 'contacts' ? 'is-active' : '' ?>">Contacte</a>
            <?php if ($currentUserId): ?>
                <a href="index.php?page=dashboard" class="<?= $page === 'dashboard' ? 'is-active' : '' ?>">Dashboard</a>
                <a href="index.php?page=cart" class="<?= $page === 'cart' ? 'is-active' : '' ?>">Cos</a>
                <a href="index.php?page=favorites" class="<?= $page === 'favorites' ? 'is-active' : '' ?>">Favorite</a>
                <a href="index.php?page=orders" class="<?= $page === 'orders' ? 'is-active' : '' ?>">Comenzi</a>
                <?php if ($userService->isAdmin($currentUserId)): ?>
                    <a href="index.php?page=dashboard-stats" class="<?= $page === 'dashboard-stats' ? 'is-active' : '' ?>">Statistici</a>
                    <a href="index.php?page=categories" class="<?= $page === 'categories' ? 'is-active' : '' ?>">Categorii</a>
                    <a href="index.php?page=products" class="<?= $page === 'products' ? 'is-active' : '' ?>">Produse</a>
                <?php endif; ?>
                <a href="index.php?page=logout">Deconectare</a>
            <?php else: ?>
                <a href="index.php?page=login" class="<?= $page === 'login' ? 'is-active' : '' ?>">Autentificare</a>
                <a href="index.php?page=register" class="btn-nav">Creeaza cont</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="page-shell">
        <?php require_once "../pages/{$page}.php"; ?>
    </main>

    <footer class="site-footer">
        <p>ePiata - experienta moderna pentru marketplace local</p>
    </footer>
</body>
</html>