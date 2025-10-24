<?php
require_once "Database.php";

class Product {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Getter for database connection
    public function getConnection() {
        return $this->conn;
    }

    // Add Product
    public function addProduct($name, $price, $quantity, $category, $image) {
        $query = "INSERT INTO products (product_name, price, quantity, category, image) 
                  VALUES (:name, :price, :quantity, :category, :image)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
    
        if ($stmt->execute()) {
            // Redirect to the admin dashboard if the insert is successful
            header("Location: ../views/admin_dashboard.php");
            exit;  // Ensure that the script stops here after the redirect
        } else {
            die("Error in Adding: " . $stmt->errorInfo()[2]); // Error message with more details
        }
    }
    
    
    // Display All Products
    public function displayProducts() {
        $query = "SELECT * FROM products";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Correct method
    }
    

    // Get Specific Product
    public function displaySpecificProduct($product_id) {
        $sql = "SELECT * FROM products WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($sql);
        
        // Correct method for PDO
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);  // Use bindParam instead of bind_param
    
        $stmt->execute();
        
        // Return the result
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    // Edit Product
    public function editProduct($product_id, $name, $price, $quantity, $category, $image) {
        $sql = "UPDATE products SET product_name = :name, price = :price, quantity = :quantity, category = :category, image = :image WHERE product_id = :product_id";
        
        $stmt = $this->conn->prepare($sql);
        
        // Bind parameters using bindParam for PDO
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            header("Location: ../views/admin_dashboard.php");
            exit;
        } else {
            die("Error in Updating: " . $stmt->errorInfo()[2]); // Use errorInfo() for PDO errors
        }
    }
    

    // Delete Product
    public function deleteProduct($product_id) {
        $sql = "DELETE FROM products WHERE product_id = :product_id";  // Use named parameter
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT); // Use bindParam for PDO
    
        if ($stmt->execute()) {
            header("Location: ../views/admin_dashboard.php");
            exit;
        } else {
            die("Error in Deleting: " . $stmt->errorInfo()[2]); // Adjust error message to PDO style
        }
    }
    

    // Get Unique Categories
    public function getCategories() {
        $query = "SELECT DISTINCT category FROM products";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Correct method
    }
    

    // Get Products by Category
    public function getProductsByCategory($category) {
        $sql = "SELECT * FROM products WHERE category = :category ORDER BY product_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Use fetchAll instead of get_result
    }

    // Reserve Stock Before Sale
    public function reserveStock($product_id, $buy_quantity) {
        $sql = "SELECT quantity FROM products WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC); // Use fetch instead of get_result
        return ($row && $row['quantity'] >= $buy_quantity);
    }

    // Adjust Stock After Sale
    public function adjustStock($product_id, $buy_quantity) {
        if ($this->reserveStock($product_id, $buy_quantity)) {
            $sql = "UPDATE products SET quantity = quantity - :buy_quantity WHERE product_id = :product_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':buy_quantity', $buy_quantity, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            return $stmt->execute();
        }
        return false;
    }

    // Get Product Price
    public function getProductPrice($product_id) {
        $sql = "SELECT price FROM products WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['price'] : 0;
    }
}
?>
