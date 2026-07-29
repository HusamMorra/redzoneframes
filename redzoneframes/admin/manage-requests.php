<?php
// lets the admin look at player requests submitted through the request-player.php form and mark each one approved or declined. the approved or declined wont do anything logic wise yet but its there for record keeping

require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../includes/db.php';

// handling a status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $requestId = (int)$_POST['request_id'];
    $newStatus = $_POST['status'];

    // only allowing real status values, same reasoning as manage-orders.php
    $allowedStatuses = ['pending', 'approved', 'declined'];
    if (in_array($newStatus, $allowedStatuses)) {
        $pdo->prepare("UPDATE player_requests SET status = ? WHERE id = ?")->execute([$newStatus, $requestId]);
    }

    header("Location: manage-requests.php?updated=1");
    exit;
}

$stmt = $pdo->query("SELECT * FROM player_requests ORDER BY created_at DESC");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Player Requests - Admin";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container section">
    <div class="admin-header">
        <h1>Player Requests</h1>
    </div>

    <nav class="admin-nav">
        <a href="dashboard.php" class="admin-nav-link">Dashboard</a>
        <a href="manage-players.php" class="admin-nav-link">Manage Players</a>
        <a href="manage-orders.php" class="admin-nav-link">Manage Orders</a>
        <a href="manage-users.php" class="admin-nav-link">Manage Users</a>
        <a href="manage-requests.php" class="admin-nav-link admin-nav-active">Player Requests</a>
        <a href="template-switcher.php" class="admin-nav-link">Site Theme</a>
		<a href="wiki/admin-guide.php" class="admin-nav-link">Help</a>
        <a href="logout.php" class="admin-nav-link admin-nav-danger">Log Out</a>
    </nav>

    <?php if (isset($_GET['updated'])): ?>
        <div class="form-message form-message-success">Request updated.</div>
    <?php endif; ?>

    <?php if (count($requests) === 0): ?>
        <p class="empty-state">No player requests submitted yet.</p>
    <?php else: ?>
	<div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Requested Player</th>
                    <th>Team</th>
                    <th>Submitted By</th>
                    <th>Reason</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
				<!-- everything under this is how we pull the info we are getting from teh form -->
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['requested_name']); ?></td>
                        <td><?php echo $request['requested_team'] ? htmlspecialchars($request['requested_team']) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($request['requester_email']); ?></td>
                        <td class="admin-table-reason"><?php echo $request['reason'] ? htmlspecialchars($request['reason']) : '-'; ?></td>
                        <td><?php echo date('M j, Y', strtotime($request['created_at'])); ?></td>
                        <td>
                            <span class="admin-status-badge admin-status-<?php echo $request['status']; ?>">
                                <?php echo ucfirst($request['status']); ?>
                            </span>
                        </td>
                        <td>
                            <form action="manage-requests.php" method="POST" class="admin-inline-form">
                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $request['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?php echo $request['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="declined" <?php echo $request['status'] === 'declined' ? 'selected' : ''; ?>>Declined</option>
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