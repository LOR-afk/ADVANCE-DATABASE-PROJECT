<?php
require_once "../classes/Product.php";

if (!isset($_GET['id'])) {
    die("Product ID is required.");
}

$product_id = $_GET['id'];
$product = new Product();
$product->deleteProduct($product_id);
?>
