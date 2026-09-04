<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

if (!$userService->isAdmin((int) $_SESSION['user_id'])) {
    http_response_code(403);
    echo 'Acces interzis.';
    exit;
}

$categories = $categoryService->read();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        http_response_code(419);
        $error = 'Sesiune invalidă. Reîmprospătează pagina.';
    } else {
        $formName = $_POST['form-name'] ?? '';

        switch ($formName) {
            case 'create-form':
                $name = trim((string) ($_POST['name'] ?? ''));
                if ($name === '') {
                    $error = 'Numele categoriei este obligatoriu.';
                } else {
                    $success = $categoryService->create($name);
                    if ($success) {
                        header('Location: index.php?page=categories');
                        exit;
                    }
                    $error = 'Nu s-a putut salva categoria.';
                }
                break;

            case 'update-form':
                $id = (int) ($_POST['id'] ?? 0);
                $name = trim((string) ($_POST['name'] ?? ''));

                if ($id <= 0 || $name === '') {
                    $error = 'Categoria și numele sunt obligatorii.';
                } else {
                    $success = $categoryService->update($id, $name);
                    if ($success) {
                        header('Location: index.php?page=categories');
                        exit;
                    }
                    $error = 'Nu s-a putut actualiza categoria.';
                }
                break;

            case 'delete-form':
                $id = (int) ($_POST['id'] ?? 0);
                if ($id <= 0) {
                    $error = 'Categoria este obligatorie.';
                } else {
                    $success = $categoryService->delete($id);
                    if ($success) {
                        header('Location: index.php?page=categories');
                        exit;
                    }
                    $error = 'Nu s-a putut șterge categoria.';
                }
                break;

            default:
                $error = 'Acțiune invalidă.';
                break;
        }
    }
}
?>

<section class="content-card">
    <p class="eyebrow">Administrare categorii</p>
    <h1>Categorii</h1>
    <p>Adaugă, actualizează sau șterge categorii de produse din catalog.</p>
</section>

<?php if ($error): ?>
    <p class="form-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<section class="stats-grid" style="margin-top:1rem;">
    <article class="stat-card">
        <h2>Adaugă categorie</h2>
        <form method="POST" class="auth-form">
            <input type="hidden" name="form-name" value="create-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="text" name="name" placeholder="Nume categorie" maxlength="30" required>
            <button class="btn btn-primary" type="submit">Adaugă</button>
        </form>
    </article>

    <article class="stat-card">
        <h2>Actualizează categorie</h2>
        <form method="POST" class="auth-form">
            <input type="hidden" name="form-name" value="update-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <select name="id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="name" placeholder="Nume nou" maxlength="30" required>
            <button class="btn btn-secondary" type="submit">Actualizează</button>
        </form>
    </article>

    <article class="stat-card">
        <h2>Șterge categorie</h2>
        <form method="POST" class="auth-form">
            <input type="hidden" name="form-name" value="delete-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <select name="id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Șterge</button>
        </form>
    </article>
</section>

<section class="content-card" style="margin-top:1rem;">
    <h2>Lista categorii</h2>
    <ul>
        <?php foreach ($categories as $category): ?>
            <li><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
</section>
