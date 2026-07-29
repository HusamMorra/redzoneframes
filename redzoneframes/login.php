<?php
// handles logging in but also handles the ?redirect=whateverpage.php so other pages like checkout.php can send someone here and it'll bring them right back to where they came from after they log in

session_start();
require_once __DIR__ . '/includes/db.php';
$errorMessage = '';
$redirectTo = isset($_GET['redirect']) ? $_GET['redirect'] : 'my-account.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? '')); //ensure its not case sensitive
    $password = $_POST['password'] ?? '';
    $redirectTo = $_POST['redirect'] ?? 'my-account.php';
   $stmt = $pdo->prepare("SELECT id, first_name, password_hash, role, is_active FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || !password_verify($password, $user['password_hash'])) { // password hash as all are protected
    $errorMessage = "Incorrect email or password.";
} elseif ($user['role'] === 'admin') {
    // i accidently tried to login with my admin login from this page and it said its incorrect so i added this where it would send them over to the actual admin login instead since i seperated them
   $errorMessage = 'Admin accounts must log in through the <a href="admin/login.php" class="text-link">Admin Login</a> page.';
} elseif (!$user['is_active']) {
    $errorMessage = "This account has been disabled. Contact us if you think this is a mistake.";
} else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_first_name'] = $user['first_name'];
        $_SESSION['user_role'] = $user['role'];
// bringing back whatever cart they had saved from last time they logged out and merging it with whatever's already in the session incase they added stuff as a guest right before logging in, dont wanna lose that
        $cartStmt = $pdo->prepare("SELECT cart_data FROM saved_carts WHERE user_id = ?");
        $cartStmt->execute([$user['id']]);
        $savedCartJson = $cartStmt->fetchColumn();
        if ($savedCartJson) {
            $savedCart = json_decode($savedCartJson, true);
            $currentCart = $_SESSION['cart'] ?? [];
            $_SESSION['cart'] = array_merge($currentCart, $savedCart);
            // clearing the saved version out now since its back in the session
            $pdo->prepare("DELETE FROM saved_carts WHERE user_id = ?")->execute([$user['id']]);
        }
        header("Location: " . $redirectTo);
        exit;
    }
}
$pageTitle = "Login - Red Zone Frames";
require_once __DIR__ . '/includes/header.php';
?>
<section class="container section auth-page">
    <div class="auth-card">
        <h1>Log In</h1>
		<!-- context sensitive help link -->
		<p class="field-hint">Need help? See our <a href="wiki/account-help.php" class="text-link">Account Help guide</a>.</p>
        <?php if ($errorMessage): ?>
            <div class="form-message form-message-error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST" class="auth-form">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTo); ?>">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            <button type="submit" class="btn btn-full">Log In</button>
        </form>
       <p class="auth-switch">Don't have an account? <a href="register.php?redirect=<?php echo urlencode($redirectTo); ?>" class="text-link">Create one</a></p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>