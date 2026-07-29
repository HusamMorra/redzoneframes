<?php
// this is the checkout page and it grabs the shipping address and shows the final breakdown which is the subtotal + tax (13%) + shipping ($5) before actually placing the order
// once submitted this makes real rows in orders and order_items, clears the cart, and sends you to the basic confirmation page
// obviously doesnt take any credit card info just saves info to database

session_start();
require_once __DIR__ . '/includes/db.php';

define('SHIPPING_FLAT_RATE', 5.00);
define('TAX_RATE', 0.13); 

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header("Location: cart.php");
    exit;
}

// to order you need to have an account
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

$errorMessage = '';

// placing the order, ask for shipping info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $streetAddress = trim($_POST['street_address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $postalCode = trim($_POST['postal_code'] ?? '');

    if ($streetAddress === '' || $city === '' || $province === '' || $postalCode === '') {
        $errorMessage = "Please fill in all shipping address fields.";
    } else {
        // recalculating everything right before placing order so the database 100% gets the right numbers its just for safety i guess
        $subtotal = 0;
        foreach ($_SESSION['cart'] as $item) {
            $subtotal += $item['unit_price'] * $item['quantity'];
        }
        $tax = $subtotal * TAX_RATE;
        $grandTotal = $subtotal + $tax + SHIPPING_FLAT_RATE;

        // inserting the order first so we get an id to attach the items to
        $orderStmt = $pdo->prepare("
            INSERT INTO orders (user_id, status, total_amount, street_address, city, province, postal_code)
            VALUES (?, 'pending', ?, ?, ?, ?, ?)
        ");
        $orderStmt->execute([$_SESSION['user_id'], $grandTotal, $streetAddress, $city, $province, $postalCode]);
        $orderId = $pdo->lastInsertId();

        // adding every cart item as its own row in order_items
        $itemStmt = $pdo->prepare("
            INSERT INTO order_items (order_id, player_id, frame_color, frame_size, custom_player_name, custom_number, add_signature, quantity, unit_price)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($_SESSION['cart'] as $item) {
            $itemStmt->execute([
                $orderId,
                $item['player_id'],
                $item['frame_color'],
                $item['frame_size'],
                $item['type'] === 'custom' ? $item['display_name'] : null,
                $item['type'] === 'custom' ? $item['display_number'] : null,
                $item['engraving_added'] ? 1 : 0,
                $item['quantity'],
                $item['unit_price']
            ]);
        }

        // delete cart
        $_SESSION['cart'] = [];
        $_SESSION['last_order_id'] = $orderId; // order-confirmation.php uses this to show what was ordered

        header("Location: order-confirmation.php");
        exit;
    }
}

// numbers to show on the page
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['unit_price'] * $item['quantity'];
}
$tax = $subtotal * TAX_RATE;
$grandTotal = $subtotal + $tax + SHIPPING_FLAT_RATE;

$pageTitle = "Checkout - Red Zone Frames";
require_once __DIR__ . '/includes/header.php';

// get their saved address if they have one and autofill the field
$userStmt = $pdo->prepare("SELECT street_address, city, province, postal_code FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$savedAddress = $userStmt->fetch(PDO::FETCH_ASSOC);
?>

<section class="container section checkout-page">
    <h1>Checkout</h1>
    <!-- context sensitive help links, both relevant right at the point of checking out -->
    <p class="field-hint">Need help? See our <a href="wiki/how-to-order.php" class="text-link">How to Order guide</a> or our <a href="wiki/shipping-faq.php" class="text-link">Shipping FAQ</a>.</p>

    <?php if ($errorMessage): ?>
        <div class="form-message form-message-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div class="checkout-grid">
        <form action="checkout.php" method="POST" class="checkout-form">
            <label for="streetAddress">Street Address</label>
            <input type="text" name="street_address" id="streetAddress" maxlength="150" required
                   value="<?php echo htmlspecialchars($savedAddress['street_address'] ?? ''); ?>">

            <label for="city">City</label>
            <input type="text" name="city" id="city" maxlength="100" required
                   value="<?php echo htmlspecialchars($savedAddress['city'] ?? ''); ?>">

            <label for="province">Province/State</label>
            <input type="text" name="province" id="province" maxlength="50" required
                   value="<?php echo htmlspecialchars($savedAddress['province'] ?? ''); ?>">

			<!-- this is to ensure the format matches a postal code or zip code -->
            <label for="postalCode">Postal/Zip Code</label>
			<input type="text" name="postal_code" id="postalCode" maxlength="10" required
      	 		pattern="^[A-Za-z][0-9][A-Za-z]\s?[0-9][A-Za-z][0-9]$|^[0-9]{5}(-[0-9]{4})?$" 
				title="Canadian postal code (e.g. A1B 2C3) or US zip code (e.g. 12345)"
      	 		value="<?php echo htmlspecialchars($savedAddress['postal_code'] ?? ''); ?>">


            <p class="field-hint">No real payment is processed. Placing the order saves it to our system as "pending."</p>

            <button type="submit" class="btn btn-full">Place Order</button>
        </form>

        <div class="checkout-summary">
            <h2>Order Summary</h2>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="checkout-summary-item">
                    <span><?php echo htmlspecialchars($item['display_name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span>$<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>

            <div class="checkout-summary-row">
                <span>Subtotal</span>
                <span>$<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="checkout-summary-row">
                <span>Shipping</span>
                <span>$<?php echo number_format(SHIPPING_FLAT_RATE, 2); ?></span>
            </div>
            <div class="checkout-summary-row">
                <span>Tax (13% HST)</span>
                <span>$<?php echo number_format($tax, 2); ?></span>
            </div>
            <div class="checkout-summary-row checkout-summary-total">
                <span>Total</span>
                <span>$<?php echo number_format($grandTotal, 2); ?></span>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>