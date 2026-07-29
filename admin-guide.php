<?php
// this is the admin side help guide, explains how to actually use the admin panel, covers the "instructions how to update contents"

require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = "Admin Guide - Red Zone Frames";
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="container section">
    <div class="admin-header">
        <h1>Admin Guide</h1>
        <p>A walkthrough of everything you can do from the admin panel.</p>
    </div>

    <nav class="admin-nav">
        <a href="../dashboard.php" class="admin-nav-link">Dashboard</a>
        <a href="../manage-players.php" class="admin-nav-link">Manage Players</a>
        <a href="../manage-orders.php" class="admin-nav-link">Manage Orders</a>
        <a href="../manage-users.php" class="admin-nav-link">Manage Users</a>
        <a href="../manage-requests.php" class="admin-nav-link">Player Requests</a>
        <a href="../template-switcher.php" class="admin-nav-link">Site Theme</a>
        <a href="admin-guide.php" class="admin-nav-link admin-nav-active">Help</a>
        <a href="../logout.php" class="admin-nav-link admin-nav-danger">Log Out</a>
    </nav>

<div class="static-page">
    <p><a href="database-guide.php" class="text-link">See the Database Guide</a> for how the data is structured.</p>
	 <p>Front-end, admin, and installation documentation can be found at the bottom of this page.</p>
	
    <h2>Dashboard</h2>
        <p>The dashboard is the landing page after logging in. It shows quick stats (total orders, pending orders, pending player requests, registered customers) and a bar chart of total sales per player, based on real orders placed through the site.</p>

        <h2>Manage Players - adding, editing, and removing catalog items</h2>
        <p>This is where you control the actual product catalog. Click "Add New Player" to create a new one, or click "Edit" next to any existing player to change their details.</p>
        <p><strong>Adding a new player:</strong></p>
        <ol>
            <li>Upload the player's image first. It needs to be a PNG file, already sitting in the images/players folder before you fill out the form, since the form only asks for the filename, not the actual image upload.</li>
            <li>Click Add New Player and fill in the name, team, jersey number, position, category (Active Roster or Retro Legend), price, and description.</li>
            <li>Type the exact image filename you uploaded, matching capitalization exactly (e.g. Allen.png).</li>
            <li>Click Add Player. The new player automatically gets the same 3 frame color options and 3 size options every other player has, so it's ready to sell immediately.</li>
        </ol>
        <p><strong>Editing or removing:</strong> click Edit to change any field on an existing player, or Delete to remove them permanently (this also removes their frame color/size options automatically).</p>

        <h2>Manage Orders</h2>
        <p>Lists every order ever placed, newest first, showing the customer, what they ordered, their shipping address, the total, and current status. Use the dropdown in the Update column to change an order's status - it saves automatically when you pick a new value, no separate save button needed.</p>

        <h2>Manage Users</h2>
        <p>Lists every registered customer account, how many orders they've placed, and whether their account is currently active or disabled. Click Disable to block someone from logging in (they'll see a message explaining their account was disabled), or Enable to restore access. You can't disable your own admin account through this page as a safety measure.</p>

        <h2>Player Requests</h2>
        <p>Shows every player suggestion submitted through the public Request a Player form. Review the requested name, team, and reason, then use the dropdown to mark it Approved or Declined. Approving a request doesn't automatically add the player, you'd still add them manually through Manage Players once you've decided to include them, this is more for tracking.</p>

        <h2>Site Theme</h2>
        <p>Lets you switch the entire public site's color scheme between Classic Theme, Night Theme, and Gold Theme. Pick one and click Save Theme, the change applies site-wide immediately for every visitor, not just for you.</p>

        <h2>Updating images</h2>
<p>To swap out a player's photo, upload the new image file to images/players through the file manager first, then go edit that player and update the Image Filename field to match.</p>

<h2>Updating videos</h2>
<p>Video content on this site is embedded from YouTube rather than hosted directly on the server. To add or replace a video:</p>
<ol>
    <li>Upload the video to YouTube as Unlisted (not Public, so it doesn't show up in search or on a channel page, but anyone with the link can view it).</li>
    <li>Once uploaded, copy the video's ID from the URL - for a link like https://youtu.be/ABC123, the ID is everything after the slash, so ABC123.</li>
    <li>Find the page where the video should appear and locate the existing iframe tag, which looks like this:
        <br><code>&lt;iframe src="https://www.youtube.com/embed/OLD_VIDEO_ID" ...&gt;&lt;/iframe&gt;</code>
    </li>
    <li>Replace OLD_VIDEO_ID with the new video's ID and save the file.</li> 
</ol>
<p>Right now there are 3 videos on the site: one on the How to Order wiki page, one on the How to Design Your Own wiki page, and one on this Admin Guide page.</p>

	 <!-- video walkthrough embedded from youtube -->
    <div class="wiki-video-wrap">
        <iframe width="700" height="394" src="https://www.youtube.com/embed/oz2C1-Sye4A" title="Admin Panel Tutorial - Red Zone Frames" frameborder="0" allowfullscreen></iframe>
    </div>
	
	<p>Additional documentation is also available in the GitHub repository:</p>
<ul>
    <li><a href="https://github.com/HusamMorra/redzoneframes/blob/main/docs/frontend-overview.md" class="text-link" target="_blank">Front-End Overview</a></li>
    <li><a href="https://github.com/HusamMorra/redzoneframes/blob/main/docs/admin-guide.md" class="text-link" target="_blank">Admin Guide (repository copy)</a></li>
    <li><a href="https://github.com/HusamMorra/redzoneframes/blob/main/docs/installation.md" class="text-link" target="_blank">Installation Guide</a></li>
</ul>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>