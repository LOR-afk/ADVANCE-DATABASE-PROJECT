<?php
session_start();
require_once "../classes/User.php";
require_once "../classes/Order.php";
require_once "../classes/Database.php"; // Database connection class

// Enable MySQLi error reporting for better debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user'])) {
    header("Location: ../views/index.php");
    exit;
}

$user = $_SESSION['user'];
$cart = $user->getCart();

if ($_POST['action'] === 'checkout') {
    $order = new Order();
    $order->setUserId($_SESSION['user']->getId());
    $order->setProducts($cart);
    $orderId = $order->createOrder(); // Create order and get order ID

    // Debugging: Check if order ID is generated
    if (!$orderId) {
        die("Error: Order ID not generated.");
    }

    // Database connection
    $db = new Database();
    $conn = $db->getConnection();

    foreach ($cart as $item) {
        $productId = (int) $item['id'];
        $quantity = (int) $item['quantity'];
        $price = (float) $item['price'];
        $total_price = $quantity * $price;
        $date = date("Y-m-d H:i:s");

        // Debugging: Print values before inserting
        echo "Order ID: $orderId, User ID: {$_SESSION['user']->getId()}, Product ID: $productId, Quantity: $quantity, Total Price: $total_price, Date: $date <br>";

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO sales (order_id, user_id, product_id, quantity, total_price, sale_date) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind parameters correctly (convert `sale_date` to string)
        if (!$stmt->bind_param("iiiids", $orderId, $_SESSION['user']->getId(), $productId, $quantity, $total_price, $date)) {
            die("Binding parameters failed: " . $stmt->error);
        }

        // Execute the query
        if (!$stmt->execute()) {
            die("Execution failed: " . $stmt->error);
        }

        $stmt->close();
    }

    // Clear cart after successful order
    $user->clearCart();

    // Redirect to receipt page
    header("Location: ../views/order-receipt.php");
    exit;
}
?>
