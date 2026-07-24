<?php
// shop page where they can see all the items offered
// they can filter by category through the url like shop.php?category=active that they press from the home page

session_start();
$pageTitle = "Shop - Red Zone Frames";
$pageDescription = "Browse the full Red Zone Frames catalog - current NFL stars and retro legends, all available as custom framed pieces.";
require_once __DIR__ . '/includes/header.php';

// checking what category to show, it opens to all if nothing got passed in
// only allowing these 3 specific values so someone cant mess with the url and break the query
$allowedCategories = ['active', 'legend', 'all'];
$selectedCategory = isset($_GET['category']) && in_array($_GET['category'], $allowedCategories)
    ? $_GET['category']
    : 'all';

// only add the category filter to the query if we're not just showing everything
if ($selectedCategory === 'all') {
    $stmt = $pdo->prepare("SELECT * FROM players WHERE is_active = 1 ORDER BY category, player_name ASC");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT * FROM players WHERE is_active = 1 AND category = ? ORDER BY player_name ASC");
    $stmt->execute([$selectedCategory]);
}
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="container section shop-header">
    <h1>Shop The Catalog</h1>
    <p>Every frame is made to order. Pick a player below to see options, pricing, and what you're actually getting.</p>
    <!-- these are just plain links with a query string, dont need js for this -->
    <div class="filter-tabs">
        <a href="shop.php?category=all" class="filter-tab <?php echo $selectedCategory === 'all' ? 'filter-tab-active' : ''; ?>">All Players</a>
        <a href="shop.php?category=active" class="filter-tab <?php echo $selectedCategory === 'active' ? 'filter-tab-active' : ''; ?>">Active Roster</a>
        <a href="shop.php?category=legend" class="filter-tab <?php echo $selectedCategory === 'legend' ? 'filter-tab-active' : ''; ?>">Retro Legends</a>
    </div>
</section>
<section class="container section section-light">
    <?php if (count($players) === 0): ?>
        <p class="empty-state">No players found in this category right now.</p>
    <?php else: ?>
        <div class="product-grid shop-grid">
            <?php foreach ($players as $player): ?>
                <div class="product-card">
                    <div class="product-card-inner">
                        <div class="product-card-front">
                            <span class="category-badge category-badge-<?php echo htmlspecialchars($player['category']); ?>">
                                <?php echo $player['category'] === 'legend' ? 'Legend' : 'Active'; ?>
                            </span>
                            <img src="images/players/<?php echo htmlspecialchars($player['image_filename']); ?>" alt="<?php echo htmlspecialchars($player['player_name']); ?> frame">
                            <h3><?php echo htmlspecialchars($player['player_name']); ?></h3>
                            <p class="product-price">$<?php echo number_format($player['base_price'], 2); ?></p>
                        </div>
                        <div class="product-card-back">
                            <h3><?php echo htmlspecialchars($player['player_name']); ?></h3>
                            <p><?php echo htmlspecialchars($player['team']); ?> &middot; #<?php echo htmlspecialchars($player['jersey_number']); ?> &middot; <?php echo htmlspecialchars($player['position']); ?></p>
                            <p class="product-desc"><?php echo htmlspecialchars($player['description']); ?></p>
                            <a href="product.php?id=<?php echo $player['id']; ?>" class="btn btn-small">View Frame</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>