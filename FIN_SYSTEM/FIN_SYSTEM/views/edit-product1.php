<?php
require_once "../classes/Product.php";
$product = new Product();

if (!isset($_GET['id'])) {
    die("Product ID is required.");
}

$product_id = $_GET['id'];
$product_data = $product->displaySpecificProduct($product_id);

if (!$product_data) {
    die("Product not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        } else {
            die("Error uploading image.");
        }
    } else {
        $image_url = $product_data['image_url'];
    }

    $product->editProduct($product_id, $product_name, $price, $quantity, $category, $image_url);
    header("Location: admin_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Product | Gym POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --bg-dark: #0f111a;
      --bg-panel: #1b1d2a;
      --accent: #2563eb;
      --accent-light: #3b82f6;
      --border: rgba(255, 255, 255, 0.1);
      --text-light: #e5e7eb;
      --text-muted: #9ca3af;
    }

    body {
      font-family: "Inter", sans-serif;
      background-color: var(--bg-dark);
      color: var(--text-light);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding-top: 60px;
    }

    .form-container {
      width: 100%;
      max-width: 640px;
      background: var(--bg-panel);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 32px 36px;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.5);
    }

    .form-container h3 {
      color: var(--accent-light);
      text-align: center;
      font-weight: 700;
      margin-bottom: 28px;
    }

    label {
      font-weight: 600;
      color: var(--text-light);
      margin-bottom: 6px;
    }

    .form-control {
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--border);
      color: var(--text-light);
      padding: 12px;
      border-radius: 10px;
      transition: 0.2s ease;
    }

    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.08);
      border-color: var(--accent);
      box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
      color: #fff;
    }

    /* 🔧 Fix for number input turning white */
    input[type=number] {
      -webkit-appearance: none;
      -moz-appearance: textfield;
      appearance: none;
      background-color: rgba(255, 255, 255, 0.05);
      color: #e5e7eb;
      border: 1px solid var(--border);
    }

    input[type=number]:focus {
      background-color: rgba(255, 255, 255, 0.08);
      color: #fff;
      border-color: var(--accent);
      box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    /* Buttons */
    .btn-primary {
      background: linear-gradient(90deg, var(--accent), var(--accent-light));
      border: none;
      font-weight: 600;
      padding: 12px 20px;
      border-radius: 10px;
      transition: 0.3s;
    }

    .btn-primary:hover {
      background: linear-gradient(90deg, var(--accent-light), var(--accent));
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
    }

    .btn-secondary {
      background-color: rgba(255, 255, 255, 0.08);
      color: var(--text-light);
      border: 1px solid rgba(255, 255, 255, 0.1);
      font-weight: 600;
      border-radius: 10px;
      transition: 0.3s;
    }

    .btn-secondary:hover {
      background-color: rgba(255, 255, 255, 0.15);
    }

    .btn-wrapper {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <div class="form-container">
    <h3>Edit Product</h3>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="product_name">Product Name</label>
        <input type="text" class="form-control" name="product_name" 
               value="<?= htmlspecialchars($product_data['product_name']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="price">Price (₱)</label>
        <input type="number" step="0.01" class="form-control" name="price" 
               value="<?= htmlspecialchars($product_data['price']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="quantity">Stock</label>
        <input type="number" class="form-control" name="quantity" 
               value="<?= htmlspecialchars($product_data['quantity']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="category">Category</label>
        <input type="text" class="form-control" name="category" 
               value="<?= htmlspecialchars($product_data['category']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="image">Product Image</label>
        <input type="file" class="form-control" name="image" accept="image/*">
        <?php if (!empty($product_data['image_url'])): ?>
          <div class="mt-2 text-center">
            <img src="<?= htmlspecialchars($product_data['image_url']) ?>" 
                 alt="Product Image" style="max-width: 150px; border-radius: 8px;">
          </div>
        <?php endif; ?>
      </div>

      <div class="btn-wrapper">
        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
