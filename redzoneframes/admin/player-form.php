<?php
// now this page is very important and works manage-players.php becasue it handles both adding a new player and editing an existing one, depending on whether ?id=X is in the URL. Keeping both in one file avoids duplicating the entire form twice for what's otherwise the exact same fields.

require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../includes/db.php';

$editId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$isEditing = $editId !== null;
$errorMessage = '';

// pre-fill values either from an existing player (editing) or blank (adding)
$player = [
    'player_name' => '',
    'team' => '',
    'jersey_number' => '',
    'position' => '',
    'category' => 'active',
    'base_price' => '',
    'description' => '',
    'image_filename' => '',
    'is_active' => 1
];

if ($isEditing) {
    $stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
    $stmt->execute([$editId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        header("Location: manage-players.php");
        exit;
    }
    $player = $existing;
}

// this handle form submission and covers both add and edit in one block. here is where for example a new players gets added to the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playerName = trim($_POST['player_name']);
    $team = trim($_POST['team']);
    $jerseyNumber = (int)$_POST['jersey_number'];
    $position = trim($_POST['position']);
    $category = $_POST['category'];
    $basePrice = (float)$_POST['base_price'];
    $description = trim($_POST['description']);
    $imageFilename = trim($_POST['image_filename']);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($playerName === '' || $team === '' || $imageFilename === '') {
        $errorMessage = "Player name, team, and image filename are required.";
    } else {
        if ($isEditing) {
            $stmt = $pdo->prepare("
                UPDATE players
                SET player_name = ?, team = ?, jersey_number = ?, position = ?, category = ?, base_price = ?, description = ?, image_filename = ?, is_active = ?
                WHERE id = ?
            ");
            $stmt->execute([$playerName, $team, $jerseyNumber, $position, $category, $basePrice, $description, $imageFilename, $isActive, $editId]);
            $newPlayerId = $editId;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO players (player_name, team, jersey_number, position, category, base_price, description, image_filename, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$playerName, $team, $jerseyNumber, $position, $category, $basePrice, $description, $imageFilename, $isActive]);
            $newPlayerId = $pdo->lastInsertId();

            // brand new players need the same 6 options every other player has (3 frame colors + 3 sizes) without this, a new player would show up on the shop page with no options to pick
            $optionSets = [
                ['frame_color', 'Black', 0.00],
                ['frame_color', 'Walnut Brown', 5.00],
                ['frame_color', 'Team Color Accent', 8.00],
                ['size', '8x10 in', 0.00],
                ['size', '11x14 in', 15.00],
                ['size', '16x20 in', 30.00]
            ];
            $optStmt = $pdo->prepare("INSERT INTO player_options (player_id, option_type, option_value, price_modifier) VALUES (?, ?, ?, ?)");
            foreach ($optionSets as $opt) {
                $optStmt->execute([$newPlayerId, $opt[0], $opt[1], $opt[2]]);
            }
        }

        header("Location: manage-players.php?saved=1");
        exit;
    }
}

$pageTitle = ($isEditing ? "Edit Player" : "Add Player") . " - Admin";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container section">
    <div class="admin-header">
        <h1><?php echo $isEditing ? 'Edit Player' : 'Add New Player'; ?></h1>
    </div>

    <?php if ($errorMessage): ?>
        <div class="form-message form-message-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form action="player-form.php<?php echo $isEditing ? '?id=' . $editId : ''; ?>" method="POST" class="admin-form">
        <label for="playerName">Player Name</label>
        <input type="text" name="player_name" id="playerName" maxlength="100" required
               value="<?php echo htmlspecialchars($player['player_name']); ?>">

     <label for="team">Team</label>
<select name="team" id="team" required>
    <option value="">Select a team</option>
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
    foreach ($nflTeams as $team):
    ?>
        <option value="<?php echo htmlspecialchars($team); ?>" <?php echo $player['team'] === $team ? 'selected' : ''; ?>><?php echo htmlspecialchars($team); ?></option>
    <?php endforeach; ?>
</select>

        <label for="jerseyNumber">Jersey Number</label>
        <input type="number" name="jersey_number" id="jerseyNumber" min="0" max="99" required
               value="<?php echo htmlspecialchars($player['jersey_number']); ?>">

        <label for="position">Position</label>
<select name="position" id="position">
    <?php
    $positions = ['QB', 'RB', 'WR', 'TE', 'OL', 'DE', 'DT', 'LB', 'CB', 'S', 'K', 'P'];
    foreach ($positions as $pos):
    ?>
        <option value="<?php echo $pos; ?>" <?php echo $player['position'] === $pos ? 'selected' : ''; ?>><?php echo $pos; ?></option>
    <?php endforeach; ?>
</select>

        <label for="category">Category</label>
        <select name="category" id="category">
            <option value="active" <?php echo $player['category'] === 'active' ? 'selected' : ''; ?>>Active Roster</option>
            <option value="legend" <?php echo $player['category'] === 'legend' ? 'selected' : ''; ?>>Retro Legend</option>
        </select>

        <label for="basePrice">Base Price ($)</label>
        <input type="number" name="base_price" id="basePrice" step="0.01" min="0" required
               value="<?php echo htmlspecialchars($player['base_price']); ?>">

        <label for="description">Description</label>
        <textarea name="description" id="description" rows="3" maxlength="500"><?php echo htmlspecialchars($player['description']); ?></textarea>

		       <!-- just a text field for the filename instead of a real upload button so the admin just uploads the image through the file manager first then types
             the exact filename in and that image will be auto updated in the shop and other areas where its displayed, this is the only thing the admin needs to use file manager for when editing site and stuff -->
		
        <label for="imageFilename">Image Filename</label>
        <input type="text" name="image_filename" id="imageFilename" maxlength="150" required
               value="<?php echo htmlspecialchars($player['image_filename']); ?>">
        <p class="field-hint">Must already be uploaded to images/players/ - e.g. "Mahomes.png"</p>

        <label class="admin-checkbox-label">
            <input type="checkbox" name="is_active" <?php echo $player['is_active'] ? 'checked' : ''; ?>>
            Visible on the shop page
        </label>

        <button type="submit" class="btn btn-full"><?php echo $isEditing ? 'Save Changes' : 'Add Player'; ?></button>
    </form>

    <a href="manage-players.php" class="btn-link">&larr; Back to Player List</a>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>