<?php
// header.php
// Included at the top of every public page. Handles:
// 1. the <head> section with SEO meta tags
// 2. pulling the active theme from the database so template switching works site-wide
// 3. the main navigation menu (responsive - collapses to a hamburger on mobile)

// every page that includes this header must already have session_start() called
// before this file is included, if it needs to know who's logged in
require_once __DIR__ . '/db.php';

// grab whichever theme the admin currently has active in site_settings
// default to classic-field if for some reason the row is missing
$stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'active_theme'");
$stmt->execute();
$activeTheme = $stmt->fetchColumn();
if (!$activeTheme) {
    $activeTheme = 'classic-field';
}

// each page can set its own title/description before including this file
// e.g. $pageTitle = "Shop - Red Zone Frames"; before require 'includes/header.php';
// if a page forgets to set one, these defaults kick in so we never ship a blank title
if (!isset($pageTitle)) {
    $pageTitle = "Red Zone Frames - Custom NFL Player Frames";
}
if (!isset($pageDescription)) {
    $pageDescription = "Shop custom-designed NFL player frames, from current stars to retro legends. Build your own design or pick from our full roster.";
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo htmlspecialchars($activeTheme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO meta tags -->
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="keywords" content="NFL frames, custom jersey frame, football gift, player frame, sports decor, Red Zone Frames">
    <meta name="author" content="Red Zone Frames">

    <!-- favicon -->
    <link rel="icon" type="image/png" href="/redzoneframes/images/favicon.png">

    <!-- google fonts: Anton for headings (bold sporty look), Inter for body text (clean/readable) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/redzoneframes/css/styles.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a href="/redzoneframes/index.php" class="logo">RED ZONE <span>FRAMES</span></a>

        <!-- hamburger button only shows on mobile, toggles .nav-open on the nav below -->
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="/redzoneframes/index.php">Home</a></li>
                <li><a href="/redzoneframes/shop.php">Shop</a></li>
                <li><a href="/redzoneframes/design-your-own.php">Design Your Own</a></li>
                <li><a href="/redzoneframes/about.php">About</a></li>
                <li><a href="/redzoneframes/contact.php">Contact</a></li>
                <li><a href="/redzoneframes/wiki/how-to-order.php">Help</a></li>
                <li><a href="/redzoneframes/cart.php" class="nav-cart">Cart</a></li>
             <?php if (isset($_SESSION['admin_id'])): ?>
    <li><a href="/redzoneframes/admin/dashboard.php" class="nav-admin-link">Admin Panel</a></li>
<?php endif; ?>
<?php if (isset($_SESSION['user_id'])): ?>
    <li><a href="/redzoneframes/my-account.php">My Account</a></li>
<?php else: ?>
    <li><a href="/redzoneframes/login.php">Login</a></li>
<?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main class="site-main">