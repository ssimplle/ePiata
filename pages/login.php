<?php
 
$error = null;
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
 
    if (empty($email) || empty($password)) {
 
        $error = "Toate campurile sunt obligatorii.";
    } else {
 
        $success = $authService->login($email, $password);
 
        if ($success) {
            header("Location: index.php?page=dashboard");
            exit;
        }
 
        $error = "Date de autentificare invalide.";
    }
}
 
?>
 
<section class="auth-wrap">
    <article class="auth-card">
        <p class="eyebrow">Bine ai revenit</p>
        <h1>Autentificare</h1>

        <?php if ($error): ?>
            <p class="form-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label for="login-email">Email</label>
            <input id="login-email" type="email" name="email" placeholder="you@example.com" required>

            <label for="login-password">Parola</label>
            <input id="login-password" type="password" name="password" placeholder="Parola ta" required>

            <button class="btn btn-primary btn-block" type="submit">Conecteaza-te</button>
        </form>

        <p class="auth-note">Nu ai cont? <a href="index.php?page=register">Creeaza unul acum</a>.</p>
    </article>
</section>