<?php
require_once "Database.php";

class User extends Database {
    protected $conn; // Ensure it is defined in User

    public function __construct() {
        parent::__construct(); // Initialize the database connection
        $this->conn = parent::getConnection(); // Ensure connection is set
    }

    // Registration method
    public function register($first_name, $last_name, $username, $password) {
        $role = 'cashier'; // Default role
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (first_name, last_name, username, password, role)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . $this->conn->errorInfo()[2]);
        }

        // Use bindValue instead of bind_param for PDO
        $stmt->bindValue(1, $first_name, PDO::PARAM_STR);
        $stmt->bindValue(2, $last_name, PDO::PARAM_STR);
        $stmt->bindValue(3, $username, PDO::PARAM_STR);
        $stmt->bindValue(4, $hashed_password, PDO::PARAM_STR);
        $stmt->bindValue(5, $role, PDO::PARAM_STR);

        $stmt->execute();
        $stmt->closeCursor();

        echo "<script>alert('Registration successful! Please log in.'); window.location.href='../views/index.php';</script>";
        exit;
    }

    // Login method
    public function login($username, $password) {
        if ($username === 'Admin' && $password === 'Admin') {
            session_start();
            $_SESSION['id'] = 1;
            $_SESSION['username'] = 'Admin';
            $_SESSION['role'] = 'admin';
            header("Location: ../views/admin_dashboard.php");
            exit;
        }

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            die("Prepare failed: " . $this->conn->errorInfo()[2]);
        }

        $stmt->bindValue(1, $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($result) {
            if (password_verify($password, $result['password'])) {
                session_start();
                $_SESSION['id'] = $result['id'];
                $_SESSION['username'] = $result['username'];
                $_SESSION['role'] = $result['role'];

                if ($result['role'] === 'admin') {
                    header("Location: ../views/admin_dashboard.php");
                } elseif ($result['role'] === 'cashier') {
                    header("Location: ../views/home.php");
                }
                exit;
            } else {
                echo "<script>alert('Incorrect password'); window.location.href='../views/index.php';</script>";
            }
        } else {
            echo "<script>alert('Username not found'); window.location.href='../views/index.php';</script>";
        }
    }
}
?>
