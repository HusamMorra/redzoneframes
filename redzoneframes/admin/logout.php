<?php
// admin/logout.php
session_start();
unset($_SESSION['admin_id'], $_SESSION['admin_first_name']);
header("Location: login.php");
exit;