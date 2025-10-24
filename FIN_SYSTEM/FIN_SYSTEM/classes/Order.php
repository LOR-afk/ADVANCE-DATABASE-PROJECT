<?php
require_once "Database.php";

class Order extends Database {
    private $userId;
    private $products = [];

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setProducts($products) {
        $this->products = $products;
    }

    // Create a new order and save the order details
    public function createOrder($userId, $orderDetails, $totalPrice) {
        $sql = "INSERT INTO orders (user_id, total_price, created_at) VALUES ('$userId', '$totalPrice', NOW())";
        
        if ($this->conn->query($sql)) {
            $orderId = $this->conn->insert_id;
            foreach ($orderDetails as $product) {
                $productId = $product['product_id'];
                $quantity = $product['quantity'];
                $price = $product['price'];
                $sql = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                        VALUES ('$orderId', '$productId', '$quantity', '$price')";
                if (!$this->conn->query($sql)) {
                    die("Error in creating order: " . $this->conn->error);
                }
            }
            return $orderId;
        } else {
            die("Error in creating order: " . $this->conn->error);
        }
    }
}
?>