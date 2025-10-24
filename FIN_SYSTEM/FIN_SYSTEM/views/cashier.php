<?php
session_start();

// Redirect if not logged in
if (empty($_SESSION)) {
    header("location: ../views/");
    exit;
}

include "../classes/Product.php";

$product = new Product();
$product_list = $product->displayProducts();
$categories = $product->getCategories();

// Handle order confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    $orders = $_POST['orders'];
    foreach ($orders as $product_id => $quantity) {
        if ($quantity > 0) {
            $total_price = $product->getProductPrice($product_id) * $quantity;
            $product->adjustStock($product_id, $quantity);
            $conn = $product->getConnection();
            $stmt = $conn->prepare("INSERT INTO sales (product_id, quantity, total_price) VALUES (?, ?, ?)");
            $stmt->bindValue(1, $product_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $quantity, PDO::PARAM_INT);
            $stmt->bindValue(3, $total_price, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
    $_SESSION['orders'] = $_POST['orders'];
    header("location: order-summary.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Select Products | Gym POS</title>

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"/>

  <style>
    :root {
      --accent: #3b82f6;
      --accent-dark: #2563eb;
      --accent-deeper: #1e40af;
      --bg1: #0f111a;
      --bg2: #1b1d2a;
      --card: rgba(24,26,38,.92);
      --card-border: rgba(255,255,255,.08);
      --muted:#a9b3c7;
    }

    * { box-sizing: border-box; }

    body {
      font-family: "Inter", sans-serif;
      background: radial-gradient(1200px 600px at 50% -10%, var(--bg2), var(--bg1));
      color: #e8ecf4;
      margin: 0;
      display: flex;
    }

    /* Sidebar */
    .sidebar {
      width: 260px;
      background: rgba(17,19,28,.85);
      backdrop-filter: blur(10px);
      border-right: 1px solid var(--card-border);
      box-shadow: 8px 0 24px rgba(0,0,0,.35);
      padding: 24px 18px;
      position: sticky;
      top: 0;
      height: 100vh;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 6px 10px;
      margin-bottom: 20px;
    }

    .brand .dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: var(--accent);
      box-shadow: 0 0 16px rgba(59,130,246,.7);
    }

    .brand span {
      font-weight: 800;
      letter-spacing: .3px;
      color: #fff;
    }

    .nav-links a {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #d7deef;
      text-decoration: none;
      padding: 12px 12px;
      border-radius: 10px;
      transition: .25s ease;
      font-weight: 600;
    }

    .nav-links a:hover {
      background: rgba(255,255,255,.08);
      color: #fff;
    }

    .nav-links a.active {
      background: linear-gradient(90deg, var(--accent-deeper), var(--accent-dark));
      color: #fff;
      box-shadow: 0 6px 20px rgba(59,130,246,.35);
    }

    /* Layout */
    .layout {
      display: grid;
      grid-template-columns: 1fr 360px;
      gap: 24px;
      width: 100%;
      padding: 36px 32px 36px 0;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 0 24px;
    }

    h1 {
      text-align: center;
      font-weight: 800;
      margin-bottom: 10px;
      color: var(--accent);
      letter-spacing: .4px;
    }

    .subtitle {
      text-align: center;
      color: var(--muted);
      margin-bottom: 24px;
    }

    /* Dropdown Fix */
    .category-filter-wrap {
      position: relative;
      z-index: 2000; /* Force dropdown on top of all */
    }

    #categoryFilter {
      width: 60%;
      background: rgba(255,255,255,.05);
      border: 1px solid var(--card-border);
      color: #fff;
      border-radius: 10px;
      padding: 10px;
      backdrop-filter: blur(8px);
      outline: none;
      transition: 0.3s ease;
      position: relative;
      z-index: 2001;
    }

    #categoryFilter option {
      background-color: #1b1d2a;
      color: white;
    }

    #categoryFilter:focus {
      border-color: var(--accent);
      box-shadow: 0 0 8px rgba(59,130,246,.4);
    }

    /* Product Grid */
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 18px;
    }

    .product-card {
      background: rgba(24,26,38,.9);
      border: 1px solid var(--card-border);
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,.25);
      transition: .25s ease;
      padding: 16px;
      text-align: center;
      color: #fff;
    }

    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 24px rgba(59,130,246,.35);
      border-color: var(--accent-dark);
    }

    .product-card img {
      border-radius: 12px;
      height: 180px;
      object-fit: cover;
      width: 100%;
      margin-bottom: 12px;
    }

    .product-card h5 {
      font-weight: 700;
      margin-bottom: 6px;
    }

    .product-card p {
      color: var(--muted);
      margin-bottom: 12px;
    }

    /* Quantity Controls */
    .quantity-controls {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 6px;
    }

    .quantity-controls input {
      width: 55px;
      text-align: center;
      border-radius: 8px;
      border: 1px solid var(--card-border);
      background: rgba(255,255,255,.05);
      color: #fff;
    }

    .btn-outline-secondary {
      background: rgba(255,255,255,.08);
      color: #fff;
      border: 1px solid var(--card-border);
    }

    .btn-outline-secondary:hover {
      background: var(--accent);
      border-color: var(--accent);
    }

    /* Order Summary */
    .order-summary {
      position: sticky;
      top: 24px;
      align-self: start;
      background: rgba(24,26,38,.95);
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 10px 30px rgba(0,0,0,.35);
      z-index: 1;
    }

    .order-summary h4 {
      color: var(--accent);
      font-weight: 700;
    }

    .order-summary .list-group-item {
      background: transparent;
      color: #fff;
      border-color: rgba(255,255,255,.12);
    }

    .btn-success {
      background: linear-gradient(90deg, #22c55e, #16a34a);
      border: 0;
      box-shadow: 0 6px 20px rgba(34,197,94,.4);
    }

    .btn-success:hover {
      background: linear-gradient(90deg, #15803d, #16a34a);
      transform: translateY(-1px);
    }
  </style>
</head>

<body>
  <aside class="sidebar">
    <div class="brand">
      <div class="dot"></div>
      <span>Gym POS</span>
    </div>

    <nav class="nav-links">
      <a href="cashier.php" class="active"><i class="fa-solid fa-house"></i> Home</a>
      <a href="../actions/logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </aside>


  <div class="layout">
    <main class="main-content">
      <h1>Select Your Products 🛒</h1>
      <p class="subtitle">Choose from our available gym equipment and supplements.</p>

      <div class="text-center mb-4 category-filter-wrap">
        <select id="categoryFilter" onchange="filterCategory()">
          <option value="all">All Categories</option>
          <?php foreach ($categories as $category) { ?>
            <option value="<?= htmlspecialchars($category['category']) ?>">
              <?= htmlspecialchars($category['category']) ?>
            </option>
          <?php } ?>
        </select>
      </div>

      <form id="orderForm" action="" method="post">
        <div class="product-grid">
          <?php foreach ($product_list as $product) { ?>
            <div class="product-card" data-category="<?= htmlspecialchars($product['category']) ?>">
              <img src="<?= htmlspecialchars($product['image']) ? htmlspecialchars($product['image']) : '../images/default-product.jpg' ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
              <h5><?= htmlspecialchars($product['product_name']) ?></h5>
              <p>₱<?= htmlspecialchars($product['price']) ?></p>
              <div class="quantity-controls">
                <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity('<?= $product['product_id'] ?>', -1)">-</button>
                <input type="number" id="quantity-<?= $product['product_id'] ?>" name="orders[<?= $product['product_id'] ?>]" value="0" min="0" max="<?= $product['quantity'] ?>" data-name="<?= htmlspecialchars($product['product_name']) ?>" data-price="<?= htmlspecialchars($product['price']) ?>">
                <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity('<?= $product['product_id'] ?>', 1)">+</button>
              </div>
            </div>
          <?php } ?>
        </div>
      </form>
    </main>

    <!-- Order Summary -->
    <aside class="order-summary">
      <h4>Order Summary</h4>
      <ul id="order-list" class="list-group"></ul>
      <h5 class="mt-3">Total: ₱<span id="total-price">0.00</span></h5>
      <div class="text-center">
        <button form="orderForm" type="submit" class="btn btn-success mt-4 w-100" name="confirm_order" onclick="return validateOrder()">Confirm Order</button>
      </div>
    </aside>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function changeQuantity(id, delta) {
      const input = document.getElementById(`quantity-${id}`);
      let q = parseInt(input.value) || 0;
      const max = parseInt(input.max);
      q = Math.max(0, Math.min(max, q + delta));
      input.value = q;
      updateSummary();
    }

    function updateSummary() {
      const list = document.getElementById("order-list");
      const total = document.getElementById("total-price");

      list.innerHTML = "";
      let totalPrice = 0;

      document.querySelectorAll("input[type=number]").forEach(input => {
        const qty = parseInt(input.value) || 0;
        if (qty > 0) {
          const name = input.dataset.name;
          const price = parseFloat(input.dataset.price) * qty;
          totalPrice += price;
          list.insertAdjacentHTML('beforeend', `<li class='list-group-item'>${name} x${qty} - ₱${price.toFixed(2)}</li>`);
        }
      });
      total.textContent = totalPrice.toFixed(2);
    }

    function filterCategory() {
      const selected = document.getElementById("categoryFilter").value;
      document.querySelectorAll(".product-card").forEach(card => {
        card.style.display = (selected === "all" || card.dataset.category === selected) ? "block" : "none";
      });
    }

    function validateOrder() {
      const hasItems = document.getElementById("order-list").innerHTML.trim() !== "";
      if (!hasItems) alert("Please select at least one product before confirming your order.");
      return hasItems;
    }

    document.addEventListener("DOMContentLoaded", updateSummary);
  </script>
</body>
</html>
