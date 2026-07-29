<?php
// design your own page that lets someone build a totally custom frame with any name, any team, any number (3 digit max), for a player thats not in our real catalog
// it has a dynamic quote calculator form, price updates live in js

session_start();
require_once __DIR__ . '/includes/db.php';

// i decided on the flat 79.99 price since we dont have real pricing for someone that isn't in our database
define('CUSTOM_BUILD_BASE_PRICE', 79.99);

$pageTitle = "Design Your Own - Red Zone Frames";
$pageDescription = "Build a fully custom NFL frame - any name, any number, any team - with your choice of frame color and size.";
require_once __DIR__ . '/includes/header.php';

// same 3 frame colors and 3 sizes as every player in the catalog uses so just getting one full set of them instead of tying it to one specific player
$optStmt = $pdo->prepare("SELECT DISTINCT option_type, option_value, price_modifier FROM player_options ORDER BY option_type, price_modifier ASC");
$optStmt->execute();
$allOptions = $optStmt->fetchAll(PDO::FETCH_ASSOC);
$frameColors = array_filter($allOptions, fn($o) => $o['option_type'] === 'frame_color');
$sizes = array_filter($allOptions, fn($o) => $o['option_type'] === 'size');
?>

<section class="container section">
    <h1>Design Your Own</h1>
	<p>Build a frame for anyone - a player we don't carry yet, a college favorite, even yourself. Fill in the details below and the price updates as you go.</p>
		<!-- context sensitive help link -->
		<p class="field-hint">Need help? See our <a href="wiki/how-to-design.php" class="text-link">Design Your Own guide</a>.</p>

    <form action="cart.php" method="POST" class="designer-form" id="designerForm">
        <input type="hidden" name="is_custom" value="1">
        <input type="hidden" name="base_price" id="basePrice" value="<?php echo CUSTOM_BUILD_BASE_PRICE; ?>">

<label for="customName">Name</label>
<input type="text" name="custom_player_name" id="customName" maxlength="10" placeholder="e.g. John Doe" required>

<label for="customNumber">Number</label>
		<!-- inputmode makes phones show the number keypad and a pattern blocks someone that somehow put a letter -->
<input type="text" name="custom_number" id="customNumber" maxlength="3" inputmode="numeric" pattern="[0-9]*" placeholder="e.g. 1" required>

<label for="customTeam">Team</label>
<select name="custom_team" id="customTeam" required>
    <option value="">Select a team</option>
    <option value="Arizona Cardinals">Arizona Cardinals</option>
    <option value="Atlanta Falcons">Atlanta Falcons</option>
    <option value="Baltimore Ravens">Baltimore Ravens</option>
    <option value="Buffalo Bills">Buffalo Bills</option>
    <option value="Carolina Panthers">Carolina Panthers</option>
    <option value="Chicago Bears">Chicago Bears</option>
    <option value="Cincinnati Bengals">Cincinnati Bengals</option>
    <option value="Cleveland Browns">Cleveland Browns</option>
    <option value="Dallas Cowboys">Dallas Cowboys</option>
    <option value="Denver Broncos">Denver Broncos</option>
    <option value="Detroit Lions">Detroit Lions</option>
    <option value="Green Bay Packers">Green Bay Packers</option>
    <option value="Houston Texans">Houston Texans</option>
    <option value="Indianapolis Colts">Indianapolis Colts</option>
    <option value="Jacksonville Jaguars">Jacksonville Jaguars</option>
    <option value="Kansas City Chiefs">Kansas City Chiefs</option>
    <option value="Las Vegas Raiders">Las Vegas Raiders</option>
    <option value="Los Angeles Chargers">Los Angeles Chargers</option>
    <option value="Los Angeles Rams">Los Angeles Rams</option>
    <option value="Miami Dolphins">Miami Dolphins</option>
    <option value="Minnesota Vikings">Minnesota Vikings</option>
    <option value="New England Patriots">New England Patriots</option>
    <option value="New Orleans Saints">New Orleans Saints</option>
    <option value="New York Giants">New York Giants</option>
    <option value="New York Jets">New York Jets</option>
    <option value="Philadelphia Eagles">Philadelphia Eagles</option>
    <option value="Pittsburgh Steelers">Pittsburgh Steelers</option>
    <option value="San Francisco 49ers">San Francisco 49ers</option>
    <option value="Seattle Seahawks">Seattle Seahawks</option>
    <option value="Tampa Bay Buccaneers">Tampa Bay Buccaneers</option>
    <option value="Tennessee Titans">Tennessee Titans</option>
    <option value="Washington Commanders">Washington Commanders</option>
</select>

        <label for="frameColor">Frame Color</label>
        <select name="frame_color" id="frameColor" required>
            <?php foreach ($frameColors as $color): ?>
                <option value="<?php echo htmlspecialchars($color['option_value']); ?>" data-modifier="<?php echo $color['price_modifier']; ?>">
                    <?php echo htmlspecialchars($color['option_value']); ?>
                    <?php if ($color['price_modifier'] > 0): ?>
                        (+$<?php echo number_format($color['price_modifier'], 2); ?>)
                    <?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="frameSize">Size</label>
        <select name="frame_size" id="frameSize" required>
            <?php foreach ($sizes as $size): ?>
                <option value="<?php echo htmlspecialchars($size['option_value']); ?>" data-modifier="<?php echo $size['price_modifier']; ?>">
                    <?php echo htmlspecialchars($size['option_value']); ?>
                    <?php if ($size['price_modifier'] > 0): ?>
                        (+$<?php echo number_format($size['price_modifier'], 2); ?>)
                    <?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>

<div class="designer-example">
    <img src="images/examplenameplate.png" alt="Example custom frame with engraved nameplate reading John Doe, Tennessee Titans, #1">
    <p class="designer-example-caption">Example: a custom build with the engraved nameplate add-on.</p>
</div>

<!-- engraving is an add on just for this. The text box only shows up once the checkbox is checked and it is handled by toggleEngravingField() in designer.js -->
<label for="addEngraving">
    <input type="checkbox" name="add_engraving" id="addEngraving" value="1" data-modifier="6.99">
    Add a custom engraved nameplate (+$6.99)
</label>
<div id="engravingTextWrap" class="custom-fields">
    <label for="engravingText">Nameplate Text</label>
    <input type="text" name="engraving_text" id="engravingText" maxlength="40" placeholder="e.g. Est. 2019, or a date/message">
    <p class="field-hint">Mounted on a small plate at the bottom of the frame. 40 characters max.</p>
</div>

        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" required>

        <div class="designer-total">
            <span>Estimated Total:</span>
            <span id="designerTotal">$<?php echo number_format(CUSTOM_BUILD_BASE_PRICE, 2); ?></span>
        </div>

        <button type="submit" class="btn btn-full">Add to Cart</button>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>