<?php
// explanation of how the database is structured, i assumed this is needed because the rubric says "MySQL database design included (see 7 below)" and 7 is about the wiki pages

require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = "Database Guide - Red Zone Frames";
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="container section">
    <div class="admin-header">
        <h1>Database Guide</h1>
        <p>A plain language explanation of how the site's data is organized.</p>
    </div>

    <nav class="admin-nav">
        <a href="../dashboard.php" class="admin-nav-link">Dashboard</a>
        <a href="../manage-players.php" class="admin-nav-link">Manage Players</a>
        <a href="../manage-orders.php" class="admin-nav-link">Manage Orders</a>
        <a href="../manage-users.php" class="admin-nav-link">Manage Users</a>
        <a href="../manage-requests.php" class="admin-nav-link">Player Requests</a>
        <a href="../template-switcher.php" class="admin-nav-link">Site Theme</a>
        <a href="database-guide.php" class="admin-nav-link admin-nav-active">Help</a>
        <a href="../logout.php" class="admin-nav-link admin-nav-danger">Log Out</a>
    </nav>

    <div class="static-page">
        <p><a href="admin-guide.php" class="text-link">See the Admin Guide</a> for how to use the admin panel.</p>

        <p>The site's database has 10 tables total. Most of them are connected to each other using foreign keys, a column in one table that points to the id of a row in another table, which is how say an order knows which customer placed it. Here's what each table stores:</p>

        <h2>users</h2>
        <p>One row per person who's registered an account, whether they're a regular customer or an admin. Stores their name, email, hashed password (never the real password), phone number, mailing address, whether their account is active or disabled, and which role they have.</p>

        <h2>players</h2>
        <p>The actual product catalog. One row per player frame available to buy, including their name, team, jersey number, position, category (active roster vs retro legend), price, description, and which image file represents them.</p>

        <h2>player_options</h2>
        <p>Stores the frame color and size choices available for each player. Every player gets the same 6 rows here (3 frame colors, 3 sizes), each linked back to that specific player by their player_id.</p>

        <h2>orders</h2>
        <p>One row per order a customer has placed, including who placed it, the date, current status, total amount, and shipping address.</p>

        <h2>order_items</h2>
        <p>One row per individual item inside an order. If someone orders 3 different frames in one checkout, that's 3 rows here, all linked back to the same order. This table also handles custom Design Your Own builds, which don't have a real matching row in the players table.</p>

        <h2>reviews</h2>
        <p>Star ratings and comments customers leave after ordering. Linked to both the player being reviewed and the customer who wrote it.</p>

        <h2>contact_messages</h2>
        <p>Built to store submissions from a contact form, but I ended up making the contact page static so it just displays an email and phone number instead of a working form, so this table exists in the database but isn't currently connected to anything. Kept in case a working contact form gets added later on.</p>

        <h2>player_requests</h2>
        <p>Player suggestions submitted through the public Request a Player form, along with whatever status an admin has given it (pending, approved, or declined).</p>

        <h2>saved_carts</h2>
        <p>Holds a customer's shopping cart contents if they log out with items still in it, so they don't lose their cart the next time they log back in.</p>

        <h2>site_settings</h2>
        <p>A small settings table. Right now it only holds one value, which of the 3 site themes is currently active.</p>

        <h2>How the tables connect</h2>
        <p>Here's how the foreign keys tie everything together:</p>
        <ul>
            <li><strong>players --> player_options:</strong> each player has multiple option rows (frame colors and sizes), linked by player_id.</li>
            <li><strong>users --> orders:</strong> each order belongs to exactly one user, linked by user_id.</li>
            <li><strong>orders --> order_items:</strong> each order can have multiple items, linked by order_id. If an order is deleted, its items are automatically deleted too (this is a cascade delete).</li>
            <li><strong>order_items --> players:</strong> each item optionally points back to a real catalog player, except for custom Design Your Own builds, where this is left blank on purpose since there's no matching player row.</li>
            <li><strong>players and users --> reviews:</strong> a review connects one player and one user together along with a rating.</li>
            <li><strong>users --> saved_carts:</strong> if a logged in user logs out with items still in their cart, it gets saved here linked to their user_id, so it can be restored the next time they log in.</li>
            <li><strong>contact_messages</strong> and <strong>player_requests</strong> both stand alone, neither links to any other table, since both are just submissions from anyone, logged in or not.</li>
            <li><strong>site_settings</strong> also stands alone, it's just a small settings table with no relationships to anything else.</li>
        </ul>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>