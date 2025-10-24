<?php
require_once "../classes/Product.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product = new Product();
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];

    // Image Upload Handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $upload_dir = "../uploads/";
        $image_path = $upload_dir . $image_name;

        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image_url = $image_path;
        } else {
            $image_url = "default.jpg";
        }
    } else {
        $image_url = "default.jpg";
    }

    $product->addProduct($product_name, $price, $quantity, $category, $image_url);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    body {
      font-family: "Inter", sans-serif;
      background: radial-gradient(circle at top, #1b1d2a, #0f111a);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #f1f1f1;
    }

    .card {
      background: rgba(24, 26, 38, 0.9);
      border: 1px solid rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(12px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
      border-radius: 20px;
      width: 100%;
      max-width: 620px;
      padding: 2.5rem 3rem;
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }

    h3 {
      text-align: center;
      color: #3b82f6;
      font-weight: 700;
      margin-bottom: 1.5rem;
      letter-spacing: 1px;
    }

    label {
      font-weight: 600;
      color: #cfd6e6;
    }

    .form-control {
      background-color: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #fff;
      padding: 12px;
      border-radius: 10px;
      transition: 0.2s ease;
    }

    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.09);
      border-color: #3b82f6;
      box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
      color: #fff;
    }

    .form-control::placeholder {
      color: #aaa;
    }

    .btn {
      padding: 12px 20px;
      border-radius: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background: linear-gradient(90deg, #2563eb, #3b82f6);
      border: none;
      color: #fff;
      box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
    }

    .btn-primary:hover {
      background: linear-gradient(90deg, #1e40af, #2563eb);
      box-shadow: 0 4px 25px rgba(59, 130, 246, 0.5);
      transform: translateY(-2px);
    }

    .btn-secondary {
      background-color: #2c2f3d;
      color: #ddd;
      border: none;
    }

    .btn-secondary:hover {
      background-color: #1e202b;
      color: #fff;
    }

    .btn-group {
      display: flex;
      justify-content: space-between;
      gap: 15px;
    }

    #imagePreview {
      display: block;
      width: 100%;
      max-height: 250px;
      object-fit: cover;
      border-radius: 12px;
      margin-top: 12px;
      border: 1px solid rgba(59, 130, 246, 0.5);
      box-shadow: 0 0 12px rgba(59, 130, 246, 0.3);
    }
  </style>
</head>
<body>
  <div class="card">
    <h3>Add Product</h3>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="product_name">Product Name</label>
        <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter product name" required>
      </div>

      <div class="mb-3">
        <label for="price">Price (₱)</label>
        <input type="number" class="form-control" id="price" name="price" step="0.01" placeholder="Enter price" required>
      </div>

      <div class="mb-3">
        <label for="quantity">Stock Quantity</label>
        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter stock quantity" required>
      </div>

      <div class="mb-3">
        <label for="category">Category</label>
        <input type="text" class="form-control" id="category" name="category" placeholder="Enter category" required>
      </div>

      <div class="mb-3">
        <label for="image">Product Image</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(event)">
        <img id="imagePreview" src="#" alt="Image Preview" style="display:none;">
      </div>

      <div class="btn-group">
        <button type="submit" class="btn btn-primary w-100">Add Product</button>
        <a href="admin_dashboard.php" class="btn btn-secondary w-100">Cancel</a>
      </div>
    </form>
  </div>

  <script>
    function previewImage(event) {
      const preview = document.getElementById('imagePreview');
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    }
  </script>
</body>
</ht
