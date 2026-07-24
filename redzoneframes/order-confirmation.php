<?php
// this page shows up right after checkout.php finishes saving an order and it reads the order id that checkout.php gave in the session and shows a summary

session_start();
require_once __DIR__ . '/includes/db.php';

// if someone just types this url in directly without actually placing an order first then theres obv no last_order_id sitting in the session, so just send them to the homepage
if (!isset($_SESSION['last_order_id'])) {
    header("Location: index.php");
    exit;
}

$orderId = $_SESSION['last_order_id'];

$orderStmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$orderStmt->execute([$orderId]);
$order = $orderStmt->fetch(PDO::FETCH_ASSOC);

// left join here since custom design your own items dont have a real player_id, so a regular join would just drop those rows from the results
$itemsStmt = $pdo->prepare("
    SELECT order_items.*, players.player_name AS catalog_player_name
    FROM order_items
    LEFT JOIN players ON players.id = order_items.player_id
    WHERE order_id = ?
");
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Order Confirmed - Red Zone Frames";
require_once __DIR__ . '/includes/header.php';
?>
<section class="container section">
    <div class="confirmation-box">
        <h1>Thanks - your order is in!</h1>
		<!-- i add R- with the 0s so it looks like a real order number and not just order #1 for example, its also explained in my-account-->
        <p>Order <strong>R-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong> has been placed and is currently <strong><?php echo ucfirst($order['status']); ?></strong>.</p>
        <p>You can check on it anytime from <a href="my-account.php">My Account</a>.</p>
        <div class="confirmation-items">
            <?php foreach ($items as $item): ?>
                <div class="confirmation-item">
                    <span><?php echo htmlspecialchars($item['custom_player_name'] ?? $item['catalog_player_name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span>$<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="confirmation-total">Total: $<?php echo number_format($order['total_amount'], 2); ?></p>
        <a href="shop.php" class="btn">Continue Shopping</a>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>