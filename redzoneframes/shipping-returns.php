<?php
// shipping-returns.php
// Static page explaining shipping cost/timing and the return policy.

$pageTitle = "Shipping & Returns - Red Zone Frames";
$pageDescription = "Red Zone Frames shipping timelines, costs, and return policy.";
require_once __DIR__ . '/includes/header.php';
?>

<section class="container section static-page">
    <h1>Shipping &amp; Returns</h1>

    <h2>Shipping</h2>
    <p>Every frame is made to order, so please allow 3-5 business days for production before your order ships. Shipping is a flat $5.00 per order regardless of how many frames you buy, and typically takes an additional 3-7 business days depending on your location.</p>

    <h2>Returns</h2>
    <p>Since every frame is custom-made, we don't accept returns. That said, if your frame arrives damaged, or if there's a production error on our end (for example: wrong name, wrong number, wrong team), contact us within 14 days of delivery and we'll remake it at no charge.</p>

    <h2>Order Tracking</h2>
    <p>You can check your order's status anytime from <a href="my-account.php">My Account</a>, where you'll see it move from Pending to Processing to Shipped to Delivered.</p>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>