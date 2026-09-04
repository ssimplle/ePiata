<?php
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query(
    'SELECT p.id, p.name, p.price, p.description, c.name AS category_name
     FROM products p
     LEFT JOIN categories c ON c.id = p.category_id
     ORDER BY p.id DESC'
)->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero">
	<p class="eyebrow">Marketplace Digital</p>
	<h1>Cumpara de la furnizori de incredere, intr-un singur loc.</h1>
	<p>
		ePiata conecteaza clientii, categoriile, furnizorii, comenzile si recenziile intr-un flux clar de cumparare.
		Explorezi produse mai rapid, salvezi favorite si gestionezi cosul fara complicatii.
	</p>
	<div class="hero-actions">
		<a class="btn btn-primary" href="index.php?page=register">Incepe acum</a>
		<a class="btn btn-secondary" href="index.php?page=about">Vezi cum functioneaza</a>
	</div>
</section>

<section class="stats-grid">
	<article class="stat-card">
		<h2>Categorii</h2>
		<p><?= count($categories) ?> categorii active in catalog.</p>
	</article>
	<article class="stat-card">
		<h2>Furnizori</h2>
		<p>Fiecare produs poate fi asociat cu profilul si datele unui furnizor.</p>
	</article>
	<article class="stat-card">
		<h2>Comenzi si Plati</h2>
		<p>Urmareste statusul de la in curs la finalizata, cu plata cash sau card.</p>
	</article>
	<article class="stat-card">
		<h2>Recenzii</h2>
		<p>Utilizatorii pot lasa note de la 1 la 5 si ii ajuta pe altii sa aleaga mai bine.</p>
	</article>
</section>

<section class="product-showcase">
	<h2>Produse Recomandate</h2>
	<div class="product-grid">
		<?php if (empty($products)): ?>
			<article class="product-card">
				<h3>Catalog gol</h3>
				<p>Adaugă produse din administrare pentru a le vedea aici.</p>
			</article>
		<?php else: ?>
			<?php foreach ($products as $product): ?>
				<article class="product-card">
					<span class="badge"><?= htmlspecialchars($product['category_name'] ?: 'Local', ENT_QUOTES, 'UTF-8') ?></span>
					<h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
					<p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></p>
					<div class="product-footer">
						<strong><?= number_format((float) $product['price'], 2, ',', '.') ?> MDL</strong>
						<form method="POST" class="product-form">
							<input type="hidden" name="add_to_cart" value="1">
							<input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
							<input type="number" name="quantity" min="1" value="1" class="product-qty">
							<button class="btn btn-mini" type="submit">Adauga in cos</button>
						</form>
					</div>
				</article>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</section>
