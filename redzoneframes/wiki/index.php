<?php
// this is the help center hub page, just links out to all the wiki topics below

$pageTitle = "Help Center - Red Zone Frames";
$pageDescription = "Step-by-step guides for ordering, designing your own frame, sizing, accounts, and shipping.";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container section static-page">
    <h1>Help Center</h1>
    <p>Pick a topic below to get a step-by-step walkthrough.</p>

    <div class="wiki-grid">
        <a href="how-to-order.php" class="wiki-card">
            <h3>How to Order</h3>
            <p>Browsing the catalog, picking options, and checking out.</p>
        </a>
        <a href="how-to-design.php" class="wiki-card">
            <h3>How to Design Your Own</h3>
            <p>Building a fully custom frame from scratch.</p>
        </a>
        <a href="sizing-help.php" class="wiki-card">
            <h3>Sizing Help</h3>
            <p>Picking the right frame size for where it'll go.</p>
        </a>
        <a href="account-help.php" class="wiki-card">
            <h3>Account Help</h3>
            <p>Registering, logging in, and tracking your orders.</p>
        </a>
        <a href="shipping-faq.php" class="wiki-card">
            <h3>Shipping FAQ</h3>
            <p>Delivery times, costs, and common questions.</p>
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>