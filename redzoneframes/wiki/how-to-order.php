<?php
// wiki page walking through the full order process

$pageTitle = "How to Order - Help Center";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container section static-page">
    <h1>How to Order</h1>
<p>This guide walks through the whole process of buying a frame from our existing catalog, from browsing to tracking your delivery.</p>

<!-- video walkthrough embedded from youtube. using an iframe embed instead of hosting the file directly since myweb's server seems to have a mime type issue that stops it from playing mp4 files correctly -->
<div class="wiki-video-wrap">
    <iframe width="700" height="394" src="https://www.youtube.com/embed/g2QJoVD4ohs" title="How to Order - Red Zone Frames" frameborder="0" allowfullscreen></iframe>
</div>

    <h2>1. Browse the catalog</h2>
    <p>Head to the Shop page from the main menu. You'll see two groups of players: the Active Roster (current NFL players) and Retro Legends (retired NFL players). Use the filter tabs at the top of the page to switch between "All Players," "Active Roster," or "Retro Legends" depending on what you're looking for. Each player card shows their name, team, and starting price at a glance, and hovering over a card flips it to show a short description before you even click in.</p>

    <h2>2. Open a player's page</h2>
    <p>Click "View Frame" on any player card to see their full product page. Here you'll find their team, jersey number, position, a description of the piece, and if the frame already has reviews, an average star rating with the number of reviews shown next to it.</p>

    <h2>3. Choose your frame color and size</h2>
    <p>Every frame comes in 3 frame colors (Black, Walnut Brown, and a Team Color Accent option) and 3 sizes (8x10, 11x14, and 16x20 inches). As you change either dropdown, the price shown on the page updates immediately to reflect your choice, so you always see the real total before committing. Larger sizes and the Team Color Accent finish cost a bit more than the base price.</p>

    <h2>4. Set a quantity and add to cart</h2>
    <p>Use the quantity field to choose how many of that exact combination (same color, same size) you want, then click Add to Cart. You'll be taken to your cart page automatically. From there you can keep shopping, anything you've already added stays in your cart while you browse other players.</p>

    <h2>5. Review your cart</h2>
    <p>Your cart page lists every item you've added, including the frame color, size, and any custom builds you've made through Design Your Own. You can adjust the quantity of any item right from this page, or remove something you've changed your mind about. The subtotal at the bottom updates as you make changes.</p>

    <h2>6. Check out</h2>
    <p>When you're ready, click Proceed to Checkout. If you're not logged in yet, you'll be asked to log in or create an account first, once you do, you'll be brought straight back to checkout instead of losing your place. On the checkout page, fill in your street address, city, province/state, and postal code. The order summary on the right shows your subtotal, a flat $5.00 shipping charge, and 13% tax, adding up to your final total. Click Place Order to finish.</p>

    <h2>7. Track your order</h2>
    <p>After placing an order, you'll land on a confirmation page showing your order number and what you bought. From then on, you can check on it anytime from My Account, where every past order is listed along with its current status: Pending, Processing, Shipped, or Delivered. Once an order is in your history, you'll also see a link to leave a star rating and review for each player you ordered.</p>

    <a href="index.php" class="text-link">Back to Help Center</a>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>