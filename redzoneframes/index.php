<?php
// ths is the home page 
// it has the base website design i built but i also made it pull top reviews for the testimonial section and a random mix of players for the preview grid

session_start();
$pageTitle = "Red Zone Frames - Custom NFL Player Frames";
$pageDescription = "Custom-made NFL player frames for the 2026 season. Shop the current roster, browse retro legends, or design your own from scratch.";
require_once __DIR__ . '/includes/header.php';

// grabbing the top 3 reviews to show as testimonials and sorting them by rating first so bas reviews dont show up and then by date so it rotates a bit instead of always being the exact same 3, its used below
$reviewStmt = $pdo->prepare("
    SELECT r.rating, r.comment, r.created_at, p.player_name, u.first_name
    FROM reviews r
    JOIN players p ON p.id = r.player_id
    JOIN users u ON u.id = r.user_id
    ORDER BY r.rating DESC, r.created_at DESC
    LIMIT 3
");
$reviewStmt->execute();
$randomReviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

// this grabs some random players from the database for the few from the catalog section
$previewStmt = $pdo->prepare("
    (SELECT * FROM players WHERE category = 'active' ORDER BY RAND() LIMIT 4)
    UNION ALL
    (SELECT * FROM players WHERE category = 'legend' ORDER BY RAND() LIMIT 2)
");
$previewStmt->execute();
$previewPlayers = $previewStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!--this is the top banner in blue on the website -->
<section class="hero">
    <div class="container hero-inner">
        <h1>Every great player deserves<br>a <span class="text-accent">frame of his own</span>.</h1>
        <p class="hero-sub">Custom NFL player frames built for every kind of fan - from this year's breakout rookies to the legends who built the league. Pick a player, pick your look, or design something nobody else has.</p>
        <div class="hero-actions">
            <a href="shop.php" class="btn">Shop the Roster</a>
            <a href="design-your-own.php" class="btn btn-accent">Design Your Own</a>
        </div>
    </div>
</section>

<!-- the 2 category cards that we have which are active players and retro legends -->
<section class="container section section-light">
    <div class="category-grid">
        <a href="shop.php?category=active" class="category-card">
            <h2>Active Roster</h2>
            <p>The guys playing right now, this season. MVPs, breakout rookies, and the names topping the stat sheet.</p>
            <span class="category-link">Shop Active &rarr;</span>
        </a>
        <a href="shop.php?category=legend" class="category-card category-card-legend">
            <h2>Retro Legends</h2>
            <p>The players who built the game before it was the game we know now. Timeless frames for a different era.</p>
            <span class="category-link">Shop Legends &rarr;</span>
        </a>
    </div>
</section>

<!-- preview grid of a few players -->
<section class="container section light">
    <h2 class="section-title">A Few From The Catalog</h2>
    <div class="product-grid">
        <?php foreach ($previewPlayers as $player): ?>
            <div class="product-card">
                <div class="product-card-inner">
                    <div class="product-card-front">
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
    <div class="section-cta">
        <a href="shop.php" class="btn">See Full Roster</a>
    </div>
</section>

<!-- testimonials section that shows actual reviews that update based on the what we did above re; rating score & date -->
<!-- fyi the &#9733 is the decimal code for the star shape -->
<section class="container section section-light">
    <h2 class="section-title">What Customers Are Saying</h2>
    <?php if (count($randomReviews) > 0): ?>
        <div class="testimonial-grid">
            <?php foreach ($randomReviews as $review): ?>
                <div class="testimonial-card">
                    <p class="testimonial-stars"><?php echo str_repeat('&#9733;', $review['rating']) . str_repeat('&#9734;', 5 - $review['rating']); ?></p>
                    <p class="testimonial-comment">"<?php echo htmlspecialchars($review['comment']); ?>"</p>
                    <p class="testimonial-meta"><?php echo htmlspecialchars($review['first_name']); ?> &middot; <?php echo htmlspecialchars($review['player_name']); ?> frame &middot; <?php echo date('M Y', strtotime($review['created_at'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="empty-state">No reviews yet - be the first to rate a frame after your order arrives.</p>
    <?php endif; ?>
</section>

<!-- request a player section -->
<section class="container section request-teaser section-accent">
    <div class="request-teaser-inner">
        <h2>Don't see your guy?</h2>
        <p>We're adding new players every month. Tell us who's missing from the catalog and we'll get them added.</p>
        <a href="request-player.php" class="btn btn-accent">Request a Player</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>