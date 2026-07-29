<?php
// this is the page that shows up after admin logs in and shows some admin things like order count, pending requests, and a bar chart of total sales per player and thetotal sales
require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../includes/db.php';

// some overall stats up top to summarize
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$pendingRequests = $pdo->query("SELECT COUNT(*) FROM player_requests WHERE status = 'pending'")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();

// total sales per player for the chart. This is used so as a business owner you can see your top sellers
// it works by joining order_items to players, grouping by player, and adding up quantity times unit_price for every line item ever ordered for that player

//i set the limit as 10 so the chart doesnt expand too much and look unpleasant 
$salesStmt = $pdo->query("
    (SELECT players.player_name AS label, SUM(order_items.quantity * order_items.unit_price) AS total_sales
     FROM order_items
     JOIN players ON players.id = order_items.player_id
     GROUP BY players.id)
    UNION ALL
    (SELECT 'Custom Builds' AS label, SUM(order_items.quantity * order_items.unit_price) AS total_sales
     FROM order_items
     WHERE order_items.player_id IS NULL)
    ORDER BY total_sales DESC
    LIMIT 10
");
$salesData = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

// adding all the players sales together for the total sales number
$totalRevenue = array_sum(array_column($salesData, 'total_sales'));

$pageTitle = "Admin Dashboard - Red Zone Frames";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container section">
    <div class="admin-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_first_name']); ?>.</p>
    </div>

    <nav class="admin-nav">
        <a href="dashboard.php" class="admin-nav-link admin-nav-active">Dashboard</a>
        <a href="manage-players.php" class="admin-nav-link">Manage Players</a>
        <a href="manage-orders.php" class="admin-nav-link">Manage Orders</a>
        <a href="manage-users.php" class="admin-nav-link">Manage Users</a>
        <a href="manage-requests.php" class="admin-nav-link">Player Requests</a>
        <a href="template-switcher.php" class="admin-nav-link">Site Theme</a>
		<a href="wiki/admin-guide.php" class="admin-nav-link">Help</a>
        <a href="logout.php" class="admin-nav-link admin-nav-danger">Log Out</a>
    </nav>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <p class="admin-stat-number"><?php echo $totalOrders; ?></p>
            <p class="admin-stat-label">Total Orders</p>
        </div>
        <div class="admin-stat-card">
            <p class="admin-stat-number"><?php echo $pendingOrders; ?></p>
            <p class="admin-stat-label">Pending Orders</p>
        </div>
        <div class="admin-stat-card">
            <p class="admin-stat-number"><?php echo $pendingRequests; ?></p>
            <p class="admin-stat-label">Player Requests</p>
        </div>
        <div class="admin-stat-card">
            <p class="admin-stat-number"><?php echo $totalUsers; ?></p>
            <p class="admin-stat-label">Registered Customers</p>
        </div>
    </div>

<div class="admin-chart-section">
        <div class="admin-chart-header">
            <h2>Total Sales Breakdown</h2>
            <div class="admin-total-sales-box">
                <span class="admin-total-sales-label">Total Sales</span>
                <span class="admin-total-sales-number">$<?php echo number_format($totalRevenue, 2); ?></span>
            </div>
        </div>
        <?php if (count($salesData) > 0): ?>
            <div class="chart-wrap">
                <canvas id="salesChart"></canvas>
            </div>
            <script>
				// this get the actual numbers and puts them in an arrow
                window.salesLabels = <?php echo json_encode(array_column($salesData, 'label')); ?>;
window.salesData = <?php echo json_encode(array_map('floatval', array_column($salesData, 'total_sales'))); ?>;
            </script>
        <?php else: ?>
            <p class="empty-state">No sales yet - this chart fills in once orders start coming in.</p>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>