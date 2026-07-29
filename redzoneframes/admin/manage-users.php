<?php
// This page allows the admin see every registered customer and also disable their account.

require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../includes/db.php';

// handle enable/disable toggle
if (isset($_GET['toggle'])) {
    $toggleId = (int)$_GET['toggle'];

    // this never let an admin accidentally disable their own account through this page
    if ($toggleId !== (int)$_SESSION['admin_id']) {
		// this chnages the is_active column in database from 1 to 0 or 0 to 1 and then in the login.php file, if its 0 then it wont allow them to login
        $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ? AND role = 'customer'")->execute([$toggleId]);
    }

    header("Location: manage-users.php?updated=1");
    exit;
}

// LEFT JOIN keeps every customer in the results even if they have zero orders since a a regular join would hide customers who haven't ordered anything yet
$stmt = $pdo->query("
    SELECT users.id, users.first_name, users.last_name, users.email, users.is_active, users.created_at,
           COUNT(orders.id) AS order_count
    FROM users
    LEFT JOIN orders ON orders.user_id = users.id
    WHERE users.role = 'customer'
    GROUP BY users.id
    ORDER BY users.created_at DESC
");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Manage Users - Admin";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container section">
    <div class="admin-header">
        <h1>Manage Users</h1>
    </div>

    <nav class="admin-nav">
        <a href="dashboard.php" class="admin-nav-link">Dashboard</a>
        <a href="manage-players.php" class="admin-nav-link">Manage Players</a>
        <a href="manage-orders.php" class="admin-nav-link">Manage Orders</a>
        <a href="manage-users.php" class="admin-nav-link admin-nav-active">Manage Users</a>
        <a href="manage-requests.php" class="admin-nav-link">Player Requests</a>
        <a href="template-switcher.php" class="admin-nav-link">Site Theme</a>
		<a href="wiki/admin-guide.php" class="admin-nav-link">Help</a>
        <a href="logout.php" class="admin-nav-link admin-nav-danger">Log Out</a>
    </nav>

    <?php if (isset($_GET['updated'])): ?>
        <div class="form-message form-message-success">Account status updated.</div>
    <?php endif; ?>

    <?php if (count($customers) === 0): ?>
        <p class="empty-state">No registered customers yet.</p>
    <?php else: ?>
	<div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Orders</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo $customer['order_count']; ?></td>
                        <td><?php echo date('M j, Y', strtotime($customer['created_at'])); ?></td>
                        <td>
                            <span class="admin-status-badge <?php echo $customer['is_active'] ? 'admin-status-active' : 'admin-status-disabled'; ?>">
                                <?php echo $customer['is_active'] ? 'Active' : 'Disabled'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="manage-users.php?toggle=<?php echo $customer['id']; ?>"
                               class="admin-action-link <?php echo $customer['is_active'] ? 'admin-action-danger' : ''; ?>"
                               onclick="return confirm('<?php echo $customer['is_active'] ? 'Disable' : 'Re-enable'; ?> this account?');">
                                <?php echo $customer['is_active'] ? 'Disable' : 'Enable'; ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
	</div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>