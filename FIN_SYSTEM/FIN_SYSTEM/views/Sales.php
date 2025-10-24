<?php
class Sales {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "1cashier_db");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Daily total sales
    public function getSalesReport() {
        $query = "SELECT DATE(created_at) as sale_date, SUM(total_price) as total_sales 
                  FROM sales 
                  GROUP BY DATE(created_at)
                  ORDER BY sale_date ASC";
        
        $sales_data = [];
        if ($result = $this->conn->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $sales_data[] = $row;
            }
        }
        return $sales_data;
    }

    // NEW: Top 5 best-selling products
    public function getTopSellingProducts() {
        $sql = "SELECT 
                    p.product_name, 
                    SUM(od.quantity) AS total_sold, 
                    SUM(od.quantity * od.price) AS total_revenue
                FROM order_details od
                JOIN products p ON od.product_id = p.product_id
                GROUP BY p.product_id
                ORDER BY total_sold DESC
                LIMIT 5";

        $data = [];
        if ($result = $this->conn->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}
?>
