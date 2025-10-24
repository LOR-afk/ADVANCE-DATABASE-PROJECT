<?php

include_once "../classes/Product.php";

// Establish PDO connection
$host = 'localhost';
$db = '1cashier_db';
$user = 'root';
$pass = '';


$uploadDir = '../uploads/';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

$product = new Product;

if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $uploadDir = '../uploads/';
        
        // Check if the upload directory exists and is writable
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            die("Upload directory does not exist or is not writable.");
        }
    
        $imagePath = $uploadDir . basename($imageName);
    
        // Move the uploaded file to the specified directory
        if (move_uploaded_file($imageTmpPath, $imagePath)) {
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO products (product_name, price, quantity, image_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$product_name, $price, $quantity, $imagePath]);
    
            // Redirect or show success message
            header("Location: ../views/manage-product.php");
            exit;
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "No file uploaded or there was an error.";
    }
    
} elseif (isset($_POST['edit_product'])) {
    $product_id = $_GET['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $product->editProduct($product_id, $product_name, $price, $quantity);
} elseif (isset($_POST['pay_product'])) {
    $product_id = $_GET['product_id'];
    $buy_quantity = $_POST['buy_quantity'];
    $product->adjustStock($product_id, $buy_quantity);
}
?>
