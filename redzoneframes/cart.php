<?php
// cart page, this lives entirely in the session, no database table for it since a cart is just temporary stuff until you actually check out
// this file does 2 jobs at once, its the landing for both the product page add to cart form and the design your own form, and its also the page that shows you whats in your cart using redirect after post here so refreshing the page doesnt double add stuff

session_start();
require_once __DIR__ . '/includes/db.php';

// making sure the cart array exists so nothing breaks trying to check it later
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// looks up the price modifier for whatever option was picked and i did this instead of trusting a price sent from the form, since someone could mess with that in the browser before submitting
function getOptionModifier($pdo, $optionType, $optionValue) {
    $stmt = $pdo->prepare("SELECT price_modifier FROM player_options WHERE option_type = ? AND option_value = ? LIMIT 1");
    $stmt->execute([$optionType, $optionValue]);
    $result = $stmt->fetchColumn();
    return $result !== false ? (float)$result : 0.00;
}

// adding something to the cart, comes from product.php or design-your-own.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['frame_color'])) {

    $frameColor = trim($_POST['frame_color']);
    $frameSize = trim($_POST['frame_size']);
    $quantity = max(1, min(10, (int)($_POST['quantity'] ?? 1))); // keeping it between 1 and 10

    $colorModifier = getOptionModifier($pdo, 'frame_color', $frameColor);
    $sizeModifier = getOptionModifier($pdo, 'size', $frameSize);

    if (isset($_POST['is_custom'])) {
        // custom build from design your own
        $basePrice = 79.99; // i hardcoded this here too, because i dont trust the hidden field its practice i saw online
        $engravingAdded = isset($_POST['add_engraving']);
        $engravingModifier = $engravingAdded ? 6.99 : 0.00;
        $unitPrice = $basePrice + $colorModifier + $sizeModifier + $engravingModifier;

        $_SESSION['cart'][] = [
            'type' => 'custom',
            'player_id' => null,
            'display_name' => trim($_POST['custom_player_name']),
            'display_team' => trim($_POST['custom_team']),
            'display_number' => trim($_POST['custom_number']),
          
            'frame_color' => $frameColor,
            'frame_size' => $frameSize,
            'engraving_added' => $engravingAdded,
            'engraving_text' => $engravingAdded ? trim($_POST['engraving_text']) : null,
            'quantity' => $quantity,
            'unit_price' => $unitPrice
        ];

    } else {
        // normal item from product.php
        $playerId = (int)($_POST['player_id'] ?? 0);

        $stmt = $pdo->prepare("SELECT player_name, team, jersey_number, base_price, image_filename FROM players WHERE id = ? AND is_active = 1");
        $stmt->execute([$playerId]);
        $player = $stmt->fetch(PDO::FETCH_ASSOC);

        // only actually add it if the player is real, incase someone messed with the id in the url/form
        if ($player) {
            $unitPrice = (float)$player['base_price'] + $colorModifier + $sizeModifier;

            $_SESSION['cart'][] = [
                'type' => 'catalog',
                'player_id' => $playerId,
                'display_name' => $player['player_name'],
                'display_team' => $player['team'],
                'display_number' => $player['jersey_number'],
                'image_filename' => $player['image_filename'],
                'frame_color' => $frameColor,
                'frame_size' => $frameSize,
                'engraving_added' => false,
                'engraving_text' => null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice
            ];
        }
    }

    // redirecting after the post so if you refresh the cart page it doesnt just add the item again
    header("Location: cart.php");
    exit;
}

// updating quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_index'])) {
    $index = (int)$_POST['update_index'];
    $newQty = max(1, min(10, (int)$_POST['new_quantity']));

    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = $newQty;
    }

    header("Location: cart.php");
    exit;
}

//removing an item
if (isset($_GET['remove'])) {
    $index = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1); // using splice instead of unset so the indexes stay in order after removing one
    }
    header("Location: cart.php");
    exit;
}

// adding up the total to display 
$cartTotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartTotal += $item['unit_price'] * $item['quantity'];
}

$pageTitle = "Your Cart - Red Zone Frames";
require_once __DIR__ . '/includes/header.php';
?>

<section class="container section">
    <h1>Your Cart</h1>

    <?php if (count($_SESSION['cart']) === 0): ?>
        <p class="empty-state">Your cart is empty. <a href="shop.php">Browse the catalog</a> or <a href="design-your-own.php">build a custom frame</a>.</p>
    <?php else: ?>
        <div class="cart-list">
            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
	<!-- custom builds dont have a real photo so this class just adjusts the layout to not leave an empty image gap -->
    <div class="cart-item <?php echo $item['type'] === 'custom' ? 'cart-item-no-image' : ''; ?>">
        <?php if ($item['type'] === 'catalog'): ?>
            <img src="images/players/<?php echo htmlspecialchars($item['image_filename']); ?>" alt="<?php echo htmlspecialchars($item['display_name']); ?>">
        <?php endif; ?>

        <div class="cart-item-details">
                        <h3><?php echo htmlspecialchars($item['display_name']); ?>
                            <?php if ($item['type'] === 'custom'): ?><span class="cart-item-tag">Custom</span><?php endif; ?>
                        </h3>
                        <p class="cart-item-meta"><?php echo htmlspecialchars($item['display_team']); ?> &middot; #<?php echo htmlspecialchars($item['display_number']); ?></p>
                        <p class="cart-item-meta"><?php echo htmlspecialchars($item['frame_color']); ?> frame &middot; <?php echo htmlspecialchars($item['frame_size']); ?></p>
                        <?php if ($item['engraving_added']): ?>
                            <p class="cart-item-meta">Engraved nameplate: "<?php echo htmlspecialchars($item['engraving_text']); ?>"</p>
                        <?php endif; ?>

                        <form action="cart.php" method="POST" class="cart-qty-form">
                            <input type="hidden" name="update_index" value="<?php echo $index; ?>">
                            <label for="qty-<?php echo $index; ?>">Qty:</label>
                            <input type="number" name="new_quantity" id="qty-<?php echo $index; ?>" value="<?php echo $item['quantity']; ?>" min="1" max="10">
                            <button type="submit" class="btn-link">Update</button>
                        </form>
                    </div>

                    <div class="cart-item-price">
                        <p class="cart-item-unit">$<?php echo number_format($item['unit_price'], 2); ?> each</p>
                        <p class="cart-item-subtotal">$<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></p>
                        <a href="cart.php?remove=<?php echo $index; ?>" class="btn-link btn-link-danger">Remove</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

<div class="cart-summary">
    <div class="cart-total-row">
        <span>Subtotal</span>
        <span class="cart-total-amount">$<?php echo number_format($cartTotal, 2); ?></span>
    </div>
    <p class="cart-summary-note">Tax and shipping calculated at checkout.</p>
    <a href="checkout.php" class="btn btn-full">Proceed to Checkout</a>
</div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>