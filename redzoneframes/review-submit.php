<?php
// review-submit.php
// Lets a logged-in customer leave a review for a frame they've ordered.
// Expects ?player_id=X in the URL, usually clicked from a "Leave a Review" link on my-account.php's order history.

session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=my-account.php");
    exit;
}

$playerId = isset($_GET['player_id']) ? (int)$_GET['player_id'] : 0;

$playerStmt = $pdo->prepare("SELECT id, player_name FROM players WHERE id = ?");
$playerStmt->execute([$playerId]);
$player = $playerStmt->fetch(PDO::FETCH_ASSOC);

if (!$player) {
    header("Location: my-account.php");
    exit;
}

// confirm this customer actually ordered this player before - never let someone
// review a product they never bought
$boughtStmt = $pdo->prepare("
    SELECT COUNT(*) FROM order_items
    JOIN orders ON orders.id = order_items.order_id
    WHERE orders.user_id = ? AND order_items.player_id = ?
");
$boughtStmt->execute([$_SESSION['user_id'], $playerId]);
$hasOrdered = $boughtStmt->fetchColumn() > 0;

// confirm they haven't already reviewed this player before
$alreadyStmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ? AND player_id = ?");
$alreadyStmt->execute([$_SESSION['user_id'], $playerId]);
$alreadyReviewed = $alreadyStmt->fetchColumn() > 0;

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hasOrdered && !$alreadyReviewed) {
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $errorMessage = "Please select a star rating.";
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO reviews (player_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $insertStmt->execute([$playerId, $_SESSION['user_id'], $rating, $comment]);

        header("Location: my-account.php?reviewed=1");
        exit;
    }
}

$pageTitle = "Review " . $player['player_name'] . " - Red Zone Frames";
require_once __DIR__ . '/includes/header.php';
?>

<section class="container section">
    <h1>Review Your <?php echo htmlspecialchars($player['player_name']); ?> Frame</h1>

    <?php if (!$hasOrdered): ?>
        <p class="empty-state">You can only review frames you've actually ordered. <a href="my-account.php">Back to My Account</a></p>
    <?php elseif ($alreadyReviewed): ?>
        <p class="empty-state">You've already reviewed this frame. <a href="my-account.php">Back to My Account</a></p>
    <?php else: ?>
        <?php if ($errorMessage): ?>
            <div class="form-message form-message-error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form action="review-submit.php?player_id=<?php echo $playerId; ?>" method="POST" class="review-form">
            <label>Rating</label>
            <div class="star-rating">
                <input type="radio" name="rating" value="5" id="star5"><label for="star5">&#9733;</label>
                <input type="radio" name="rating" value="4" id="star4"><label for="star4">&#9733;</label>
                <input type="radio" name="rating" value="3" id="star3"><label for="star3">&#9733;</label>
                <input type="radio" name="rating" value="2" id="star2"><label for="star2">&#9733;</label>
                <input type="radio" name="rating" value="1" id="star1"><label for="star1">&#9733;</label>
            </div>

            <label for="comment">Your Review (optional)</label>
            <textarea name="comment" id="comment" rows="4" maxlength="500"></textarea>

            <button type="submit" class="btn btn-full">Submit Review</button>
        </form>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>