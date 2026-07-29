<?php
// wiki page covering registration, login, profile editing, and reviews.

$pageTitle = "Account Help - Help Center";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container section static-page">
    <h1>Account Help</h1>
    <p>Everything you need to know about creating an account, managing it, and using it to track your orders.</p>

    <h2>Creating an account</h2>
    <p>Click Login in the main navigation menu, then click "Create one" on the login page. You'll need to provide your first name, last name, an email address, and a password of at least 8 characters, entered twice to confirm it matches. Once submitted, you're automatically logged in, no separate confirmation step needed.</p>

    <h2>Logging in</h2>
    <p>Return to the same Login page and enter your email and password. If you tried to check out while logged out, the site remembers where you were headed - after logging in (or creating a new account on the spot), you'll be brought straight back to your cart or checkout instead of having to start over.</p>

    <h2>Editing your profile</h2>
    <p>From My Account, click Edit Profile. Here you can update your first name, last name, phone number, and mailing address (street address, city, province or state, and postal code) at any time. Your email address is tied to your login and can't be changed from this page - if you need it updated, reach out through the Contact page.</p>

    <h2>Viewing your order history</h2>
    <p>My Account displays every order you've placed, newest first, along with each order's current status and total. Each order also lists exactly what was purchased: the player, frame color, size, and quantity so you can always confirm what you ordered.</p>

    <h2>Leaving a review</h2>
    <p>Once an order appears in your history, you'll see a "Leave a review" link for each catalog player included in that order (custom Design Your Own builds aren't reviewable since they're not part of the regular catalog). Clicking it lets you choose a star rating from 1 to 5 and optionally write a comment. You can only review a player once per order, and only after you've actually ordered them, this keeps reviews genuine and tied to real purchases.</p>

    <h2>Logging out</h2>
    <p>Click Log Out from My Account whenever you're done. If you had items sitting in your cart while logged in, they're automatically saved to your account and will be waiting for you the next time you log back in, even from a different device.</p>

    <a href="index.php" class="text-link">Back to Help Center</a>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>