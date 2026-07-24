<?php
// db.php itself is not included in this repo since it holds real database credentials

define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name_here');
define('DB_USER', 'your_database_username_here');
define('DB_PASS', 'your_database_password_here');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    error_log("DB connection failed: " . $e->getMessage());
    die("Sorry, something went wrong connecting to the database. Please try again later.");
}
