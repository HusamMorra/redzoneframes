<?php
// this page allows the admin to edit or delete players and also add players. This is made so it can be done in the website and wont require like code editinig except adding images to the folder

require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../includes/db.php';

// handling delete
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    // player_options has on delete cascade set up, so deleting a player here automatically cleans up their frame color/size option rows too, dont need to manually delete those separately to avoid it being complex for non programming admins
    $pdo->prepare("DELETE FROM players WHERE id = ?")->execute([$deleteId]);
    header("Location: manage-players.php?deleted=1");
    exit;
}

$stmt = $pdo->query("SELECT * FROM players ORDER BY category, player_name ASC");
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Manage Players - Admin";
require_once __DIR__ . '/../includes/header.php';
?>
<section class="container section">
    <div class="admin-header">
        <h1>Manage Players</h1>
    </div>
    <nav class="admin-nav">
        <a href="dashboard.php" class="admin-nav-link">Dashboard</a>
        <a href="manage-players.php" class="admin-nav-link admin-nav-active">Manage Players</a>
        <a href="manage-orders.php" class="admin-nav-link">Manage Orders</a>
        <a href="manage-users.php" class="admin-nav-link">Manage Users</a>
        <a href="manage-requests.php" class="admin-nav-link">Player Requests</a>
        <a href="template-switcher.php" class="admin-nav-link">Site Theme</a>
        <a href="logout.php" class="admin-nav-link admin-nav-danger">Log Out</a>
    </nav>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="form-message form-message-success">Player deleted.</div>
    <?php endif; ?>
    <?php if (isset($_GET['saved'])): ?>
        <div class="form-message form-message-success">Player saved.</div>
    <?php endif; ?>
    <a href="player-form.php" class="btn">Add New Player</a>
<div class="admin-table-wrap">
<table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Team</th>
                <th>Category</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player): ?>
                <tr>
                    <td><img src="../images/players/<?php echo htmlspecialchars($player['image_filename']); ?>" alt="" class="admin-table-thumb"></td>
                    <td><?php echo htmlspecialchars($player['player_name']); ?></td>
                    <td><?php echo htmlspecialchars($player['team']); ?></td>
                    <td><?php echo ucfirst($player['category']); ?></td>
                    <td>$<?php echo number_format($player['base_price'], 2); ?></td>
                    <td class="admin-table-actions">
                       <a href="player-form.php?id=<?php echo $player['id']; ?>" class="admin-action-link">Edit</a>
<a href="manage-players.php?delete=<?php echo $player['id']; ?>" class="admin-action-link admin-action-danger" onclick="return confirm('Delete this player? This cannot be undone.');">Delete</a>
						
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>