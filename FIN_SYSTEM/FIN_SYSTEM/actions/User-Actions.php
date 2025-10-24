<?php
include "../classes/User.php";

$user = new User();

if (isset($_POST['register'])) {
    // Sanitize and validate input
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];

    // Check if fields are not empty
    if (empty($first_name) || empty($last_name) || empty($username) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href='../views/register.php';</script>";
        exit;
    }

    // Call register method
    $user->register($first_name, $last_name, $username, $password);

} elseif (isset($_POST['login'])) {
    // Sanitize and validate input
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];

    // Check if fields are not empty
    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href='../views/index.php';</script>";
        exit;
    }

    // Call login method
    $user->login($username, $password);
}
?>
