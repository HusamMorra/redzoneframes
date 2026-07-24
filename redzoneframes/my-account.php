<?php
// this is the account page, shows your info and past orders and if youre not logged in and trying to buy an item, it just sends you to the login page

session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=my-account.php");
    exit;
}

$stmt = $pdo->prepare("SELECT first_name, last_name, email, phone, street_address, city, province, postal_code, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// grab all their past orders, newest first
$orderStmt = $pdo->prepare("SELECT id, order_date, status, total_amount FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$orderStmt->execute([$_SESSION['user_id']]);
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "My Account - Red Zone Frames";
require_once __DIR__ . '/includes/header.php';
?>

<section class="container section">
    <h1><?php echo isset($_SESSION['is_new_account']) ? 'Welcome' : 'Welcome back'; ?>, <?php echo htmlspecialchars($user['first_name']); ?></h1>
<?php unset($_SESSION['is_new_account']); // dont want it saying welcome every time, just the first login after making the account ?>

	<?php if (isset($_GET['reviewed'])): ?>
    <div class="form-message form-message-success">Thanks for your review!</div>
<?php endif; ?>


    <div class="account-grid">
        <div class="account-card">
            <h2>Account Details</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'Not set'; ?></p>

            <p><strong>Address:</strong>
			<?php
	// address is split into 4 separate columns, so this combines whichever ones actually have something typed in and joins them with commas into one line. array_filter removes any empty ones
    		$addressParts = array_filter([$user['street_address'], $user['city'], $user['province'], $user['postal_code']]);
   			 echo count($addressParts) > 0 ? htmlspecialchars(implode(', ', $addressParts)) : 'Not set';
    			?>
					</p>
            <p><strong>Member since:</strong> <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
            <a href="edit-profile.php" class="btn-link">Edit Profile</a>
			<br>
			 <a href="logout.php" class="btn-link btn-link-danger">Log Out</a>
        </div>

        <div class="account-card">
            <h2>Order History</h2>
            <?php if (count($orders) === 0): ?>
                <p class="empty-state">No orders yet. <a href="shop.php">Start shopping</a>.</p>
            <?php else: ?>
	
<?php foreach ($orders as $order): ?>
	<div class="order-block">
    <div class="order-row">
        <div>

		<!-- just padding the id with 0s so it looks like a real order number instead of like order #4 because it may look like thats the users fourth order but they only ahd 1 order -->
         <p><strong>Order #R-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong></p>
            <p class="order-meta"><?php echo date('M j, Y', strtotime($order['order_date'])); ?></p>
        </div>
        <div>
            <span class="order-status order-status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
        </div>
        <div class="order-total">$<?php echo number_format($order['total_amount'], 2); ?></div>
    </div>

<?php
// grabbing everything that was in this order so we can list it out below
// left join because custom orders dont have a real player_id in the players table so we just say the custom name
$itemsDisplayStmt = $pdo->prepare("
    SELECT order_items.*, players.player_name AS catalog_player_name
    FROM order_items
    LEFT JOIN players ON players.id = order_items.player_id
    WHERE order_items.order_id = ?
");
$itemsDisplayStmt->execute([$order['id']]);
$orderLineItems = $itemsDisplayStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="order-items-list">
    <?php foreach ($orderLineItems as $lineItem): ?>
        <p class="order-item-line">
            <?php echo htmlspecialchars($lineItem['custom_player_name'] ?? $lineItem['catalog_player_name']); ?>
            (<?php echo htmlspecialchars($lineItem['frame_color']); ?>, <?php echo htmlspecialchars($lineItem['frame_size']); ?>)
            x<?php echo $lineItem['quantity']; ?>
            - $<?php echo number_format($lineItem['unit_price'] * $lineItem['quantity'], 2); ?>
        </p>
    <?php endforeach; ?>
</div>
    <?php
    // only pulling catalog players here (not custom builds) since those are the only ones you can leave a review for
    $orderItemsStmt = $pdo->prepare("
        SELECT DISTINCT players.id, players.player_name
        FROM order_items
        JOIN players ON players.id = order_items.player_id
        WHERE order_items.order_id = ?
    ");
    $orderItemsStmt->execute([$order['id']]);
    $orderPlayers = $orderItemsStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <?php if (count($orderPlayers) > 0): ?>
        <div class="order-review-links">
            <?php foreach ($orderPlayers as $orderPlayer): ?>
                <a href="review-submit.php?player_id=<?php echo $orderPlayer['id']; ?>" class="admin-action-link">
                    Leave a review on: <?php echo htmlspecialchars($orderPlayer['player_name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
	</div>
<?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>