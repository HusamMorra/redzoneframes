<?php
// wiki page explaining the design your own tool

$pageTitle = "How to Design Your Own - Help Center";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container section static-page">
    <h1>How to Design Your Own</h1>
    <p>The Design Your Own tool lets you order a frame for a player who isn't in our regular catalog, or build something completely custom just for yourself.</p>

	<!-- video walkthrough from youtube -->
		<div class="wiki-video-wrap">
    	<iframe width="700" height="394" src="https://www.youtube.com/embed/grevQRd7AEY" title="How to Design Your Own - Red Zone Frames" frameborder="0" 		 allowfullscreen></iframe>
		</div>

    <h2>1. Go to Design Your Own</h2>
    <p>You'll find this link in the main navigation menu, and also as a button on the homepage next to Shop the Roster.</p>

    <h2>2. Enter the name</h2>
    <p>Type in whatever name you want on the frame. This is limited to 10 characters, so keep it short, a last name usually works best.</p>

    <h2>3. Enter the jersey number</h2>
    <p>Type in a number, up to 3 digits. This field only accepts numbers, so letters won't be accepted here.</p>

    <h2>4. Pick a team</h2>
    <p>Choose from all 32 NFL teams in the dropdown. This makes sure every custom frame uses a real, correctly spelled team name.</p>

    <h2>5. Pick a frame color and size</h2>
    <p>Same 3 frame colors and 3 sizes every catalog player uses. There's a photo example on the page showing what a finished custom frame with these options looks like, including the engraved nameplate described below.</p>

    <h2>6. Add an engraved nameplate (optional)</h2>
    <p>Check this box if you want a small engraved plate mounted at the bottom of the frame. A text box will appear where you can type up to 40 characters - this could be a date, a short message, or anything else you'd like included. This is a stylized engraving effect that's part of the frame's design, added for $6.99.</p>

    <h2>7. Check the price and add to cart</h2>
    <p>The Estimated Total at the bottom of the form updates automatically as you change any option or the quantity, so you'll always see the full price before adding it to your cart.</p>

    <h2>A note on pricing</h2>
    <p>Custom builds use a flat base price of $79.99 since we don't have real market pricing data for a player outside our catalog. If the player you're building actually already exists in our shop, you'll always get a better price by ordering them directly from the Shop page instead of through Design Your Own.</p>

    <a href="index.php" class="text-link">Back to Help Center</a>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>