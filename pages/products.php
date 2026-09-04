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
$suppliers = $pdo->query('SELECT id, company_name FROM suppliers ORDER BY company_name ASC')->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query(
    'SELECT p.id, p.name, p.price, p.description, p.category_id, c.name AS category_name, p.supplier_id, s.company_name AS supplier_name
     FROM products p
     LEFT JOIN categories c ON c.id = p.category_id
     LEFT JOIN suppliers s ON s.id = p.supplier_id
     ORDER BY p.id DESC'
)->fetchAll(PDO::FETCH_ASSOC);

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        http_response_code(419);
        $error = 'Sesiune invalidă. Reîncarcă pagina.';
    } else {
        $formName = $_POST['form-name'] ?? '';

        if ($formName === 'create-product') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $price = (float) ($_POST['price'] ?? 0);
            $description = trim((string) ($_POST['description'] ?? ''));
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $supplierId = (int) ($_POST['supplier_id'] ?? 0);

            if ($name === '' || $price <= 0 || $description === '' || $categoryId <= 0) {
                $error = 'Toate câmpurile obligatorii trebuie completate.';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO products (name, price, description, category_id, supplier_id) VALUES (:name, :price, :description, :category_id, :supplier_id)'
                );
                $success = $stmt->execute([
                    ':name' => $name,
                    ':price' => $price,
                    ':description' => $description,
                    ':category_id' => $categoryId,
                    ':supplier_id' => $supplierId > 0 ? $supplierId : null,
                ]);

                if ($success) {
                    header('Location: index.php?page=products');
                    exit;
                }
                $error = 'Nu s-a putut adăuga produsul.';
            }
        }
    }
}
?>

<section class="content-card">
    <p class="eyebrow">Administrare produse</p>
    <h1>Produse în catalog</h1>
    <p>Adaugă articolele disponibile pentru cumpărare și pregătește prezentarea pentru analiza finală.</p>
</section>

<?php if ($error): ?>
    <p class="form-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<section class="stats-grid" style="margin-top:1rem;">
    <article class="stat-card">
        <h2>Adaugă produs</h2>
        <form method="POST" class="auth-form">
            <input type="hidden" name="form-name" value="create-product">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="text" name="name" placeholder="Numele produsului" required>
            <input type="number" name="price" step="0.01" min="0.01" placeholder="Preț" required>
            <textarea name="description" rows="3" placeholder="Descriere produs" required></textarea>
            <select name="category_id" required>
                <option value="">Selectează categoria</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <select name="supplier_id">
                <option value="0">Fără furnizor</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= (int) $supplier['id'] ?>"><?= htmlspecialchars($supplier['company_name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Salvează produs</button>
        </form>
    </article>
</section>

<section class="product-showcase" style="margin-top:1rem;">
    <h2>Produse disponibile</h2>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <article class="product-card">
                <span class="badge"><?= htmlspecialchars($product['category_name'] ?: 'Catalog', ENT_QUOTES, 'UTF-8') ?></span>
                <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="product-footer">
                    <strong><?= number_format((float) $product['price'], 2, ',', '.') ?> MDL</strong>
                    <span><?= htmlspecialchars($product['supplier_name'] ?: 'Furnizor local', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
