<?php
session_start();

// Redirect to login page if not logged in
if (empty($_SESSION)) {
    header("location: ../views/");
    exit;
}

include "../classes/Product.php";

$product = new Product();
$product_list = $product->displayProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Selection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Light background */
        }
        .card {
            margin-top: 20px;
        }
        .card-header {
            background-color: #007bff; /* Blue header */
            color: white;
        }
        .btn-confirm {
            background-color: #28a745; /* Green confirm button */
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white justify-content-between">
        <a href="dashboard.php" class="ms-3" title="Home">
            <i class="fa-solid fa-house fa-2x text-dark"></i>
        </a>
        <a href="profile.php" class="nav-link text-secondary">
            <span class="fw-bold fs-5">Welcome, <?= ucfirst($_SESSION['username'])?></span>
        </a>
        <a href="../actions/logout.php" class="me-3" title="Logout"><i class="fa-solid fa-user-xmark fa-2x text-danger"></i></a>
    </nav>

    <div class="container mt-5">
        <div class="card w-75 border-0 mx-auto">
            <div class="card-header">
                <h1 class="display-6 fw-bold">Select Products</h1>
            </div>
            <div class="card-body">
                <?php if(empty($product_list)): ?>
                    <div class="alert alert-danger text-center">
                        <strong>No Products Available</strong>
                    </div>
                <?php else: ?>
                    <form action="../actions/checkout.php" method="post">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($product_list as $item): ?>
                                    <tr>
                                        <td><?= $item['id'] ?></td>
                                        <td><?= $item['product_name'] ?></td>
                                        <td><?= $item['price'] ?></td>
                                        <td>
                                            <input type="number" name="quantity[<?= $item['id'] ?>]" min="1" max="<?= $item['quantity'] ?>" value="1" required>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-confirm" name="confirm_order" value="<?= $item['id'] ?>">Confirm</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>