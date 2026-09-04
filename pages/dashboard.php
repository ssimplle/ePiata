<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$user = $userService->getUser($userId);
$stats = $userService->getDashboardStats();
?>

<section class="content-card">
    <p class="eyebrow">Zona Utilizator</p>
    <h1>Dashboard</h1>
    <p>
        Bine ai venit<?= $user && $user['type'] === 'ADMIN' ? ', administrator' : '' ?>!<br>
        Aici gestionezi favoritele, coșul, comenzile și datele contului.
    </p>
</section>

<section class="stats-grid">
    <article class="stat-card">
        <h2>Favorite</h2>
        <p>Salvează produsele la care vrei să revii rapid.</p>
        <p><a href="index.php?page=favorites">Vezi favorite</a></p>
    </article>
    <article class="stat-card">
        <h2>Coș</h2>
        <p>Ajustează cantitatea și continuă către plată.</p>
        <p><a href="index.php?page=cart">Deschide coșul</a></p>
    </article>
    <article class="stat-card">
        <h2>Comenzi</h2>
        <p>Urmărește fiecare comandă de la procesare la finalizare.</p>
        <p><a href="index.php?page=orders">Istoric comenzi</a></p>
    </article>
    <article class="stat-card">
        <h2>Recenzii</h2>
        <p>Evaluează și comentează produsele după livrare.</p>
    </article>
</section>

<?php if ($user && $user['type'] === 'ADMIN'): ?>
    <section class="stats-grid" style="margin-top:1rem;">
        <article class="stat-card">
            <h2>Utilizatori</h2>
            <p><?= (int) $stats['users'] ?></p>
        </article>
        <article class="stat-card">
            <h2>Categorii</h2>
            <p><?= (int) $stats['categories'] ?></p>
        </article>
        <article class="stat-card">
            <h2>Produse</h2>
            <p><?= (int) $stats['products'] ?></p>
        </article>
        <article class="stat-card">
            <h2>Comenzi</h2>
            <p><?= (int) $stats['orders'] ?></p>
        </article>
    </section>
<?php endif; ?>

