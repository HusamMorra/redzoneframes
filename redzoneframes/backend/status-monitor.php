<?php
// basic check page that tests a few of the sites core services like the database, shop page and home page. It basically reports online/offline for each one.

$checks = [];

// check 1: database connection
try {
    require_once __DIR__ . '/../includes/db.php';
    $checks['Database Connection'] = true;
} catch (Exception $e) {
    $checks['Database Connection'] = false;
}

// check 2: players table actually has data in it
if (isset($pdo)) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn();
        $checks['Product Catalog'] = $count > 0;
    } catch (Exception $e) {
        $checks['Product Catalog'] = false;
    }
} else {
    $checks['Product Catalog'] = false;
}

//check 3: key pages still exist on disk. This is a simple file_exists check, just confirms none of the core pages have gone missing or gotten accidentally deleted/renamed
$keyPages = [
    'Homepage' => __DIR__ . '/../index.php',
    'Shop Page' => __DIR__ . '/../shop.php',
    'Cart Page' => __DIR__ . '/../cart.php',
    'Checkout Page' => __DIR__ . '/../checkout.php',
    'Login Page' => __DIR__ . '/../login.php'
];
foreach ($keyPages as $label => $path) {
    $checks[$label] = file_exists($path);
}

//heck 4: images folder is reachable
$checks['Product Images Folder'] = is_dir(__DIR__ . '/../images/players');

$pageTitle = "Site Status Monitor";
require_once __DIR__ . '/../includes/header.php';
?>
<section class="container section">
    <h1>Site Status Monitor</h1>
    <div class="status-list">
        <?php foreach ($checks as $label => $isOnline): ?>
            <div class="status-row">
                <span class="status-dot <?php echo $isOnline ? 'status-dot-online' : 'status-dot-offline'; ?>"></span>
                <span class="status-label"><?php echo htmlspecialchars($label); ?></span>
                <span class="status-text <?php echo $isOnline ? 'status-text-online' : 'status-text-offline'; ?>">
                    <?php echo $isOnline ? 'Online' : 'Offline'; ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
