<?php 
session_start();

if (empty($_SESSION)) {
    header("location: ../views/");
    exit;
}

include "../classes/Product.php";

$product = new Product;

$product_list = $product->displayProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #e6f2e6; /* Light background for the dashboard */
        }
        .navbar {
            background-color: #2f8d2f; /* Dark green navbar */
        }
        .navbar a {
            color: white;
        }
        .navbar-text {
            font-weight: bold;
            font-size: 20px;
        }
        .product-card {
            border: solid 1px green;
            background-color: #d0d9d4; 
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .product-card img {
            height: 200px;
            width: auto;
            object-fit: contain; 
            margin-bottom: 15px; 
        }
        .product-card:hover {
            border: 1px solid black;
            transform: scale(1.05);
        }
        .btn-custom {
            background-color: #048720; /* Custom button color */
            color: #FFFFFF;
        }
        .btn-custom:hover {
            background-color: #0056b3; /* Darker shade on hover */
        }
        .description {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .btn-danger {
            margin-right: 1rem;
        }
        .dropdown-menu {
            background-color: #2f8d2f; /* Dropdown background color */
        }
        .dropdown-item:hover {
            background-color: #0056b3; /* Dropdown item hover color */
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a href="manage-product.php" class="navbar-brand">Your Daily Cravings</a>
        <span class="navbar-text ms-3">Welcome, <?= ucfirst($_SESSION['username']) ?></span>
        <div class="ms-auto">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    ☰
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="admin_dashboard.php">Dashboard</a></li>
                    <li><a class="dropdown-item" href="manage-product.php">Manage Products</a></li>
                    <li><a class="dropdown-item" href="manage-users.php">Manage Users</a></li>
                    <li><a class="dropdown-item" href="transaction_history.php">Transaction History</a></li>
                    <li><a class="dropdown-item" href="cashier.php">Cashier View</a></li>
                </ul>
            </div>
        </div>
        <a href="../actions/logout.php" class="btn btn-danger ms-3">Logout</a>
    </div>
</nav>

    <div class="container mt-5">
        <h1 class="display-6 fw-bold text-center">Manage Products</h1>
        <div class="text-end mb-3">
            <button class="btn btn-info" style="border: 2px solid black; color: white; background-color: #04873d;" data-bs-toggle="modal" data-bs-target="#add-product">Add Product</button>
        </div>
        
        <div class="row">
            <?php if (empty($product_list)): ?>
                <div class="col text-center">
                    <h1 class="display-6 fw-bold text-danger">No Records Found</h1>
                    <i class="fa-regular fa-circle-xmark fa-8x pb-5"></i>
                </div>
            <?php else: ?>
                <?php foreach ($product_list as $product): ?>
                    <div class="col-md-4">
                        <div class="product-card">
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['image_url']) ?>" class="img-fluid" />
                            <h5 class="fw-bold"><?= htmlspecialchars($product['product_name']) ?></h5>
                            <div class="description">Price: ₱<?= htmlspecialchars($product['price']) ?></div>
                            <div class="description">Quantity: <?= htmlspecialchars($product['quantity']) ?></div>
                            <div class="d-flex justify-content-between">
                                <a href="edit-product.php?product_id=<?= $product['product_id'] ?>" class="btn btn-warning btn-sm" title="Edit Product"><i class="fa-solid fa-pen"></i></a>
                                <a href="../actions/delete-product.php?product_id=<?= $product['product_id'] ?>" class="btn btn-danger btn-sm" title="Delete Product"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ADD PRODUCT MODAL -->
    <div class="modal fade" id="add-product" tabindex="-1" aria-labelledby="registration" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <h1 class="display-4 fw-bold text-info text-center"><i class="fa-solid fa-box"></i> Add Product</h1>

                    <form action="../actions/product-actions.php" method="post" class="w-75 mx-auto pt-4 p-5" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md">
                                <label for="product-name" class="form-label small text-secondary">Product Name</label>
                                <input type="text" name="product_name" id="product-name" class="form-control" required>
                                <label for="image" class="form-label small text-secondary">Image Upload</label>
                                <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price" class="form-label small text-secondary">Price</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="price-tag">₱</span>
                                    <input type="number" name="price" id="price" class="form-control" aria-label="Price" aria-describedby="price-tag" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="quantity" class="form-label small text-secondary">Quantity</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <button type="submit" class="btn btn-info w-100" name="add_product">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz2txYfIi5y26GV0yX6EYl7xlgL/yLs5d5p6V6uJt9lD5eE5WKlwYdx6PK" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>