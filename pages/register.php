<?php
 
$error = null;
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
 
    if (empty($email) || empty($password)) {
 
        $error = "Toate campurile sunt obligatorii.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
 
        $error = "Adresa de email este invalida.";
    } elseif (strlen($password) < 8) {
 
        $error = "Parola trebuie sa aiba cel putin 8 caractere.";
    } else {
 
        $success = $authService->register($email, $password);
 
        if ($success) {
            header("Location: index.php?page=login");
            exit;
        }
 
        $error = "Inregistrarea a esuat. Incearca din nou.";
    }
}
 
?>
 
<section class="auth-wrap">
    <article class="auth-card">
        <p class="eyebrow">Alatura-te ePiata</p>
        <h1>Creeaza cont</h1>

        <?php if ($error): ?>
            <p class="form-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label for="register-email">Email</label>
            <input id="register-email" type="email" name="email" placeholder="you@example.com" required>

            <label for="register-password">Parola</label>
            <input id="register-password" type="password" name="password" placeholder="Minimum 8 caractere" required>

            <button class="btn btn-primary btn-block" type="submit">Inregistreaza-te</button>
        </form>

        <p class="auth-note">Ai deja cont? <a href="index.php?page=login">Mergi la autentificare</a>.</p>
    </article>
</section>