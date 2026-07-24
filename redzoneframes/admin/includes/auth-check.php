<?php
// this page included at the top of every page inside admin/ and kicks anyone out who isnt logged in as an admin straight to the admin login page. this way i dont have to copy paste this same check into every single admin page manually

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}