<?php
// Lets the admin pick which of the 3 CSS themes is active and it swiches immedietly
// Updates the single row in site_settings that header.php already reads on every page load to set <html data-theme="...">.

require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../includes/db.php';

// these are the three themes. i  got the default look from the NFL logo colors
$availableThemes = [
    'classic-theme' => [
        'name' => 'Classic Theme',
        'description' => 'Clean light background, navy and red. This is the default look.'
    ],
    'night-theme' => [
        'name' => 'Night Theme',
        'description' => 'Dark background with a neon lime accent. Allows for a modern feel.'
    ],
    'gold-theme' => [
        'name' => 'Gold Theme',
        'description' => 'Bold black and gold, festive big-game energy.'
    ]
];

// handle theme change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme'])) {
    $selectedTheme = $_POST['theme'];

    // whitelist check that only allow values that are actually real themes so it never trusts a theme name coming straight from a form submission so it doenst error out
    if (array_key_exists($selectedTheme, $availableThemes)) {
        $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'active_theme'");
        $stmt->execute([$selectedTheme]);
    }

    header("Location: template-switcher.php?saved=1");
    exit;
}

// find out which theme is currently active
$currentThemeStmt = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'active_theme'");
$currentTheme = $currentThemeStmt->fetchColumn();

$pageTitle = "Site Theme - Admin";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container section">
    <div class="admin-header">
        <h1>Site Theme</h1>
        <p>Choose which color scheme and layout the whole public site uses.</p>
    </div>

    <nav class="admin-nav">
        <a href="dashboard.php" class="admin-nav-link">Dashboard</a>
        <a href="manage-players.php" class="admin-nav-link">Manage Players</a>
        <a href="manage-orders.php" class="admin-nav-link">Manage Orders</a>
        <a href="manage-users.php" class="admin-nav-link">Manage Users</a>
        <a href="manage-requests.php" class="admin-nav-link">Player Requests</a>
        <a href="template-switcher.php" class="admin-nav-link admin-nav-active">Site Theme</a>
        <a href="logout.php" class="admin-nav-link admin-nav-danger">Log Out</a>
    </nav>

    <?php if (isset($_GET['saved'])): ?>
        <div class="form-message form-message-success">Site theme updated. Visit the public site to see it applied.</div>
    <?php endif; ?>

    <form action="template-switcher.php" method="POST" class="theme-picker">
        <?php foreach ($availableThemes as $themeKey => $theme): ?>
            <label class="theme-option <?php echo $currentTheme === $themeKey ? 'theme-option-active' : ''; ?>">
                <input type="radio" name="theme" value="<?php echo $themeKey; ?>" <?php echo $currentTheme === $themeKey ? 'checked' : ''; ?>>
                <div class="theme-swatch theme-swatch-<?php echo $themeKey; ?>"></div>
                <div class="theme-option-text">
                    <h3><?php echo $theme['name']; ?></h3>
                    <p><?php echo $theme['description']; ?></p>
                </div>
            </label>
        <?php endforeach; ?>

        <button type="submit" class="btn">Save Theme</button>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>