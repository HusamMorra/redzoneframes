<?php
// here we make sure that before wiping the session, save whatever's in the cart to the database first or else session_destroy() would just delete your cart completely

session_start();
require_once __DIR__ . '/includes/db.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
// json_encode turns the whole cart array into one string so it can fit into a single text column instead of needing a separate table for it
    $cartJson = json_encode($_SESSION['cart']);

// this saves the cart, but if this user already has a saved cart from before (like they logged out with stuff in their cart, logged back in, added more stuff, then logged out again) ON DUPLICATE KEY UPDATE just overwrites the old one instead of trying to insert a second row and avoiding that the user_id will be a duplicate
    $stmt = $pdo->prepare("
        INSERT INTO saved_carts (user_id, cart_data)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE cart_data = ?, updated_at = CURRENT_TIMESTAMP
    ");
    $stmt->execute([$_SESSION['user_id'], $cartJson, $cartJson]);
}

session_unset();
session_destroy();
header("Location: index.php");
exit;