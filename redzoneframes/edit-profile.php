<?php
// lets a logged in customer update their name, phone, and address. 
// email cant be changed here on purpose, since thats tied to login and allowing change adds more complexity

session_start();
require_once __DIR__ . '/includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=edit-profile.php");
    exit;
}
$stmt = $pdo->prepare("SELECT first_name, last_name, email, phone, street_address, city, province, postal_code FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$errorMessage = '';
$successMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
$phone = trim($_POST['phone']);
    $streetAddress = trim($_POST['street_address']);
    $city = trim($_POST['city']);
    $province = trim($_POST['province']);
    $postalCode = trim($_POST['postal_code']);
    if ($firstName === '' || $lastName === '') {
        $errorMessage = "First and last name are required.";
    } else {
        $updateStmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, street_address = ?, city = ?, province = ?, postal_code = ? WHERE id = ?");
        $updateStmt->execute([$firstName, $lastName, $phone, $streetAddress, $city, $province, $postalCode, $_SESSION['user_id']]);
        $_SESSION['user_first_name'] = $firstName;
        $successMessage = "Profile updated.";
		// just updating these locally so the form shows the new values right away without a refresh
        $user['first_name'] = $firstName;
        $user['last_name'] = $lastName;
        $user['phone'] = $phone;
        $user['street_address'] = $streetAddress;
        $user['city'] = $city;
        $user['province'] = $province;
        $user['postal_code'] = $postalCode;
    }
}
$pageTitle = "Edit Profile - Red Zone Frames";
require_once __DIR__ . '/includes/header.php';
?>
<section class="container section auth-page">
    <div class="auth-card">
        <h1>Edit Profile</h1>
        <?php if ($successMessage): ?>
            <div class="form-message form-message-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="form-message form-message-error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>
        <form action="edit-profile.php" method="POST" class="auth-form">
            
			<label for="firstName">First Name</label>
            <input type="text" name="first_name" id="firstName" maxlength="50" required
                   value="<?php echo htmlspecialchars($user['first_name']); ?>">
          
			<label for="lastName">Last Name</label>
            <input type="text" name="last_name" id="lastName" maxlength="50" required
                   value="<?php echo htmlspecialchars($user['last_name']); ?>">
          
			<label for="email">Email</label>
            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
           
			<p class="field-hint">Contact us if you need to change your email address.</p>
            <label for="phone">Phone</label>
            <input type="tel" name="phone" id="phone" maxlength="20"
                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
			
           <label for="streetAddress">Street Address</label>
            <input type="text" name="street_address" id="streetAddress" maxlength="150"
                   value="<?php echo htmlspecialchars($user['street_address'] ?? ''); ?>">

            <label for="city">City</label>
            <input type="text" name="city" id="city" maxlength="100"
                   value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">

            <label for="province">Province/State</label>
            <input type="text" name="province" id="province" maxlength="50"
                   value="<?php echo htmlspecialchars($user['province'] ?? ''); ?>">

           <label for="postalCode">Postal/Zip Code</label>
			<input type="text" name="postal_code" id="postalCode" maxlength="10"
      		 pattern="^[A-Za-z][0-9][A-Za-z]\s?[0-9][A-Za-z][0-9]$|^[0-9]{5}(-[0-9]{4})?$"
       			title="Canadian postal code (e.g. A1C 2C3) or US zip code (e.g. 12345)"
       			value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
            <button type="submit" class="btn btn-full">Save Changes</button>
        </form>
        <a href="my-account.php" class="btn-link">&larr; Back to My Account</a>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>