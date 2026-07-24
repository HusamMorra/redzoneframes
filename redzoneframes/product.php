<?php
// this is the single product page and it expects ?id= number in the url
// gets the players info plus their frame color and size options, and shows reviews

session_start();
require_once __DIR__ . '/includes/db.php';

// set to 0 if no id was passed, that way the query below just comes back empty 
$playerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM players WHERE id = ? AND is_active = 1");
$stmt->execute([$playerId]);
$player = $stmt->fetch(PDO::FETCH_ASSOC);

// if the id doesnt match a real player just show a message and stop here
if (!$player) {
    $pageTitle = "Player Not Found - Red Zone Frames";
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="container section"><p class="empty-state">That frame doesn\'t exist or isn\'t available anymore. <a href="shop.php">Back to shop</a></p></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = htmlspecialchars($player['player_name']) . " Frame - Red Zone Frames";
$pageDescription = "Custom " . htmlspecialchars($player['player_name']) . " frame - " . htmlspecialchars($player['team']) . ". Pick your frame color and size.";
require_once __DIR__ . '/includes/header.php';

// grabbing all the options for the player then splitting them into 2 groups with array_filter so the dropdowns below are easy to build separately
$optStmt = $pdo->prepare("SELECT * FROM player_options WHERE player_id = ? ORDER BY id ASC");
$optStmt->execute([$playerId]);
$allOptions = $optStmt->fetchAll(PDO::FETCH_ASSOC);

$frameColors = array_filter($allOptions, fn($o) => $o['option_type'] === 'frame_color');
$sizes = array_filter($allOptions, fn($o) => $o['option_type'] === 'size');

// getting reviews for this player, newest first
$reviewStmt = $pdo->prepare("
    SELECT r.rating, r.comment, r.created_at, u.first_name
    FROM reviews r
    JOIN users u ON u.id = r.user_id
    WHERE r.player_id = ?
    ORDER BY r.created_at DESC
");
$reviewStmt->execute([$playerId]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
$avgRating = count($reviews) > 0 ? round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1) : null;
?>

<section class="container section product-detail">
    <div class="product-detail-grid">
        <div class="product-detail-image">
            <span class="category-badge category-badge-<?php echo htmlspecialchars($player['category']); ?>">
                <?php echo $player['category'] === 'legend' ? 'Legend' : 'Active'; ?>
            </span>
            <img src="images/players/<?php echo htmlspecialchars($player['image_filename']); ?>" alt="<?php echo htmlspecialchars($player['player_name']); ?> frame">
        </div>

        <div class="product-detail-info">
            <h1><?php echo htmlspecialchars($player['player_name']); ?></h1>
            <p class="product-meta"><?php echo htmlspecialchars($player['team']); ?> &middot; #<?php echo htmlspecialchars($player['jersey_number']); ?> &middot; <?php echo htmlspecialchars($player['position']); ?></p>

            <?php if ($avgRating !== null): ?>
                <p class="product-rating">&#9733; <?php echo $avgRating; ?>/5 (<?php echo count($reviews); ?> review<?php echo count($reviews) === 1 ? '' : 's'; ?>)</p>
            <?php endif; ?>

            <p class="product-description"><?php echo htmlspecialchars($player['description']); ?></p>

            <p class="product-price-large" id="displayPrice">$<?php echo number_format($player['base_price'], 2); ?></p>

				<!-- this is the dynamic form where price updates live with js as you change color/size, handled in main.js -->
            <form action="cart.php" method="POST" class="add-to-cart-form" id="addToCartForm">
                <input type="hidden" name="player_id" value="<?php echo $player['id']; ?>">
                <input type="hidden" name="base_price" value="<?php echo $player['base_price']; ?>" id="basePrice">

                <label for="frameColor">Frame Color</label>
                <select name="frame_color" id="frameColor" required>
                    <?php foreach ($frameColors as $color): ?>
                        <option value="<?php echo htmlspecialchars($color['option_value']); ?>" data-modifier="<?php echo $color['price_modifier']; ?>">
                            <?php echo htmlspecialchars($color['option_value']); ?>
                            <?php if ($color['price_modifier'] > 0): ?>
                                (+$<?php echo number_format($color['price_modifier'], 2); ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="frameSize">Size</label>
                <select name="frame_size" id="frameSize" required>
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?php echo htmlspecialchars($size['option_value']); ?>" data-modifier="<?php echo $size['price_modifier']; ?>">
                            <?php echo htmlspecialchars($size['option_value']); ?>
                            <?php if ($size['price_modifier'] > 0): ?>
                                (+$<?php echo number_format($size['price_modifier'], 2); ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" required>

                <button type="submit" class="btn btn-full">Add to Cart - <span id="btnPrice">$<?php echo number_format($player['base_price'], 2); ?></span></button>
            </form>
        </div>
    </div>

    <!-- materials info, same blurb on every product page -->
    <div class="materials-box">
        <h2>What You're Getting</h2>
        <p>Every Red Zone Frame is made to order, not mass-produced. 
			The player graphic is UV-printed directly onto matboard for sharp, fade-resistant color, then mounted inside a real wood-grain shadowbox frame with a clear acrylic front panel. The Frame is sized and priced for a wall, not a full jersey. Hanging hardware is included on the back, ready to mount out of the box.</p>
        <p>Production takes 3-5 business days before shipping, since each one is printed and assembled after an order is placed.</p>
    </div>

    <!-- reviews section -->
    <div class="reviews-section">
        <h2>Customer Reviews</h2>
        <?php if (count($reviews) === 0): ?>
            <p class="empty-state">No reviews yet for this frame - be the first once your order arrives.</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <p class="review-rating"><?php echo str_repeat('&#9733;', $review['rating']) . str_repeat('&#9734;', 5 - $review['rating']); ?></p>
                    <p class="review-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                    <p class="review-author">- <?php echo htmlspecialchars($review['first_name']); ?>, <?php echo date('M Y', strtotime($review['created_at'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>