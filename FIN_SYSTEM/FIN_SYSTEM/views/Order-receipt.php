<?php
session_start();
require_once "../classes/User.php";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Receipt</title>
</head>
<body>
    <h1>Order Receipt</h1>
    <p>Thank you for your order, <?php echo $user->getUsername(); ?>!</p>
    <p>Your order has been placed successfully.</p>
    <a href="product_selection.php">Create Another Order</a>
    <a href="../actions/Logout.php">Logout</a>
</body>
</html>
