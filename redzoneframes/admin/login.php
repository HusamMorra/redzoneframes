<?php
// separate login just for admin that way its kept apart from the regular customer login so admin auth logic isnt mixed in with customer stuff

session_start();
require_once __DIR__ . '/../includes/db.php';
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // the role = admin part here matters a lot, this is what stops a regular customer account from being able to log in through this page at all, even if they somehow knew this url existed
    $stmt = $pdo->prepare("SELECT id, first_name, password_hash, role, is_active FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
	
    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        $errorMessage = "Incorrect email or password.";
    } elseif (!$admin['is_active']) {
        $errorMessage = "This admin account has been disabled.";
    } else {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_first_name'] = $admin['first_name'];
        header("Location: dashboard.php");
        exit;
    }
}
$pageTitle = "Admin Login - Red Zone Frames";
require_once __DIR__ . '/../includes/header.php';
?>
<section class="container section auth-page">
    <div class="auth-card">
        <h1>Admin Login</h1>
        <?php if ($errorMessage): ?>
            <div class="form-message form-message-error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST" class="auth-form">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            <button type="submit" class="btn btn-full">Log In</button>
        </form>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>