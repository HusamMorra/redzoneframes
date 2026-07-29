<?php
// new customer signup page

session_start();
require_once __DIR__ . '/includes/db.php';

$errorMessage = '';
$redirectTo = isset($_GET['redirect']) ? $_GET['redirect'] : (isset($_POST['redirect']) ? $_POST['redirect'] : 'my-account.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // checking all this server side too, cant  rely on the html required attributes
    if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
        $errorMessage = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $errorMessage = "Password must be at least 8 characters.";
    } elseif ($password !== $confirmPassword) {
        $errorMessage = "Passwords don't match.";
    } else {
        // making sure this email isnt already used by someone else
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);

        if ($checkStmt->fetch()) {
            $errorMessage = "An account with that email already exists. Try logging in instead.";
        } else {
            // PASSWORD_DEFAULT just lets php pick whatever the strongest hashing method currently is instead of me hardcoding one. all passwords are hashed prior to being added
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $pdo->prepare("
                INSERT INTO users (first_name, last_name, email, password_hash, role)
                VALUES (?, ?, ?, ?, 'customer')
            ");
            $insertStmt->execute([$firstName, $lastName, $email, $passwordHash]);

            // this logs the customer in right away instead of making them log in again
           $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_first_name'] = $firstName;

            // this flag makes my-account.php say "welcome" instead of "welcome back" just for the first time
            $_SESSION['is_new_account'] = true;

            header("Location: " . $redirectTo);
            exit;
        }
    }
}

$pageTitle = "Create Account - Red Zone Frames";
require_once __DIR__ . '/includes/header.php';
?>

<section class="container section auth-page">
    <div class="auth-card">
       <h1>Create Your Account</h1>
	<!-- context sensitive help link -->
			<p class="field-hint">Need help? See our <a href="wiki/account-help.php" class="text-link">Account Help guide</a>.</p>

        <?php if ($errorMessage): ?>
            <div class="form-message form-message-error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

       <form action="register.php?redirect=<?php echo urlencode($redirectTo); ?>" method="POST" class="auth-form">
    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTo); ?>">
            <label for="firstName">First Name</label>
            <input type="text" name="first_name" id="firstName" maxlength="50" required
                   value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">

            <label for="lastName">Last Name</label>
            <input type="text" name="last_name" id="lastName" maxlength="50" required
                   value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            <label for="password">Password</label>
            <input type="password" name="password" id="password" minlength="8" required>
            <p class="field-hint">At least 8 characters.</p>

            <label for="confirmPassword">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirmPassword" minlength="8" required>

            <button type="submit" class="btn btn-full">Create Account</button>
        </form>

      <p class="auth-switch">Already have an account? <a href="login.php?redirect=<?php echo urlencode($redirectTo); ?>" class="text-link">Log in</a></p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>