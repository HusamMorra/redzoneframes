<?php
// lists every order placed on the site and gives the admin ability to edit the status

require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../includes/db.php';

// handling a status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['status'];

    // only allowing values that are real, i dont want to trust status from the url without checking
    $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($newStatus, $allowedStatuses)) {
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$newStatus, $orderId]);
    }

    header("Location: manage-orders.php?updated=1");
    exit;
}

// here we joined users so we can show the customers name/email next to each order
// grabbing the split address columns too so we can show shipping info in the table too
$stmt = $pdo->query("
    SELECT orders.id, orders.order_date, orders.status, orders.total_amount,
           orders.street_address, orders.city, orders.province, orders.postal_code,
           users.first_name, users.last_name, users.email
    FROM orders
    JOIN users ON users.id = orders.user_id
    ORDER BY orders.order_date DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Manage Orders - Admin";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container section">
    <div class="admin-header">
        <h1>Manage Orders</h1>
    </div>

    <nav class="admin-nav">
        <a href="dashboard.php" class="admin-nav-link">Dashboard</a>
        <a href="manage-players.php" class="admin-nav-link">Manage Players</a>
        <a href="manage-orders.php" class="admin-nav-link admin-nav-active">Manage Orders</a>
        <a href="manage-users.php" class="admin-nav-link">Manage Users</a>
        <a href="manage-requests.php" class="admin-nav-link">Player Requests</a>
        <a href="template-switcher.php" class="admin-nav-link">Site Theme</a>
        <a href="logout.php" class="admin-nav-link admin-nav-danger">Log Out</a>
    </nav>

    <?php if (isset($_GET['updated'])): ?>
        <div class="form-message form-message-success">Order status updated.</div>
    <?php endif; ?>

    <?php if (count($orders) === 0): ?>
        <p class="empty-state">No orders placed yet.</p>
    <?php else: ?>
	<div class="admin-table-wrap">
        <table class="admin-table">
			<thead>
		    <tr>
		        <th>Order #</th>
		        <th>Customer</th>
		        <th>Items</th>
		        <th>Shipping Address</th>
		        <th>Date</th>
		        <th>Total</th>
		        <th>Status</th>
		        <th>Update</th>
		    </tr>
		</thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
						<!-- same thing as myaccount, added R- before and some 0s padded -->
                        <td>R-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                      <td>
                            <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?><br>
                            <span class="admin-table-sub"><?php echo htmlspecialchars($order['email']); ?></span>
                        </td>
                        <td>
                            <?php
                            // here we get whats actually in this specific order, same idea as how my-account.php lists items under each order in the customers history 
	//left join again since custom design-your-own items dont have a real player_id
                            $itemsStmt = $pdo->prepare("
                                SELECT order_items.*, players.player_name AS catalog_player_name
                                FROM order_items
                                LEFT JOIN players ON players.id = order_items.player_id
                                WHERE order_items.order_id = ?
                            ");
                            $itemsStmt->execute([$order['id']]);
                            $orderLineItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($orderLineItems as $lineItem):
                            ?>
                                <p class="admin-table-order-item">
                                    <?php echo htmlspecialchars($lineItem['custom_player_name'] ?? $lineItem['catalog_player_name']); ?>
                                    (<?php echo htmlspecialchars($lineItem['frame_color']); ?>, <?php echo htmlspecialchars($lineItem['frame_size']); ?>) x<?php echo $lineItem['quantity']; ?>
                                </p>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php
                            // same combining trick as my-account.php, just showing whatever address parts actually have something typed in
                            $addressParts = array_filter([$order['street_address'], $order['city'], $order['province'], $order['postal_code']]);
                            echo count($addressParts) > 0 ? htmlspecialchars(implode(', ', $addressParts)) : 'Not provided';
                            ?>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <span class="order-status order-status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                        </td>
                        <td>
                            <form action="manage-orders.php" method="POST" class="admin-inline-form">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
	</div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>