<?php
// this is our 2nd required dynamic form (design-your-owns price calculator was the 1st). this page lets people suggest a player thats not in the catalog yet because we only have 20 players right now
// once they submit one it has player_requests as pending, and gets reviewed later in the admin panel

session_start();
require_once __DIR__ . '/includes/db.php';

$successMessage = '';
$errorMessage = '';

// this page both shows the form and handles the submission, keeping it in one file instead of needing a whole separate process script for it
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestedName = trim($_POST['requested_name'] ?? '');
    $requestedTeam = trim($_POST['requested_team'] ?? '');
    $requesterEmail = trim($_POST['requester_email'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    // checking this server side too
    if ($requestedName === '' || $requesterEmail === '') {
        $errorMessage = "Player name and email are required.";
    } elseif (!filter_var($requesterEmail, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
    } else {
        $insertStmt = $pdo->prepare("
            INSERT INTO player_requests (requested_name, requested_team, requester_email, reason)
            VALUES (?, ?, ?, ?)
        ");
        $insertStmt->execute([$requestedName, $requestedTeam, $requesterEmail, $reason]);
        $successMessage = "Thanks! We've logged your request for " . htmlspecialchars($requestedName) . " and our team will take a look.";
    }
}

$pageTitle = "Request a Player - Red Zone Frames";
$pageDescription = "Don't see your player in the catalog? Let us know who's missing and we'll consider adding them.";
require_once __DIR__ . '/includes/header.php';
?>

<section class="container section request-page">
    <h1>Request a Player</h1>
    <p>We're always looking to expand the catalog. Tell us who's missing and why, and our team reviews every request - if there's enough interest, we'll get them added.</p>

    <?php if ($successMessage): ?>
        <div class="form-message form-message-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="form-message form-message-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form action="request-player.php" method="POST" class="request-form">
        <label for="requestedName">Player Name *</label>
        <input type="text" name="requested_name" id="requestedName" maxlength="100" required
               value="<?php echo isset($_POST['requested_name']) ? htmlspecialchars($_POST['requested_name']) : ''; ?>">

        <label for="requestedTeam">Team (if known)</label>
<select name="requested_team" id="requestedTeam">
    <option value="">Not sure / not applicable</option>
    <?php
    $nflTeams = [
        'Arizona Cardinals', 'Atlanta Falcons', 'Baltimore Ravens', 'Buffalo Bills',
        'Carolina Panthers', 'Chicago Bears', 'Cincinnati Bengals', 'Cleveland Browns',
        'Dallas Cowboys', 'Denver Broncos', 'Detroit Lions', 'Green Bay Packers',
        'Houston Texans', 'Indianapolis Colts', 'Jacksonville Jaguars', 'Kansas City Chiefs',
        'Las Vegas Raiders', 'Los Angeles Chargers', 'Los Angeles Rams', 'Miami Dolphins',
        'Minnesota Vikings', 'New England Patriots', 'New Orleans Saints', 'New York Giants',
        'New York Jets', 'Philadelphia Eagles', 'Pittsburgh Steelers', 'San Francisco 49ers',
        'Seattle Seahawks', 'Tampa Bay Buccaneers', 'Tennessee Titans', 'Washington Commanders'
    ];
    $selectedTeam = isset($_POST['requested_team']) ? $_POST['requested_team'] : '';
    foreach ($nflTeams as $team):
    ?>
        <option value="<?php echo htmlspecialchars($team); ?>" <?php echo $selectedTeam === $team ? 'selected' : ''; ?>><?php echo htmlspecialchars($team); ?></option>
    <?php endforeach; ?>
</select>

        <label for="requesterEmail">Your Email *</label>
        <input type="email" name="requester_email" id="requesterEmail" required
               value="<?php echo isset($_POST['requester_email']) ? htmlspecialchars($_POST['requester_email']) : ''; ?>">
        <p class="field-hint">Only used if we want to let you know when this player gets added.</p>

        <label for="reason">Why should we add them? (optional)</label>
        <textarea name="reason" id="reason" rows="4" maxlength="500"><?php echo isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : ''; ?></textarea>

        <button type="submit" class="btn btn-full">Submit Request</button>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>