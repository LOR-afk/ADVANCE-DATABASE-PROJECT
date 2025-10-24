<?php 
session_start(); 
include "../classes/Product.php";
include_once "Sales.php";

$product = new Product();
$sales = new Sales();
$product_list = $product->displayProducts();
$sales_data = $sales->getSalesReport();

if (!isset($_SESSION['login_time'])) {
    $_SESSION['login_time'] = date("Y-m-d H:i:s");
}
if (!isset($_SESSION['logout_time'])) {
    $_SESSION['logout_time'] = "Still Active";
}
$isActive = ($_SESSION['logout_time'] === "Still Active");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Gym POS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root {
  --accent:#3b82f6;
  --accent-dark:#2563eb;
  --accent-deep:#1e40af;
  --bg1:#0f111a;
  --bg2:#1b1d2a;
  --card:#161821;
}

body {
  margin:0;
  font-family:"Inter",sans-serif;
  background:radial-gradient(1600px 800px at 50% -10%,var(--bg2),var(--bg1));
  color:#e8ecf4;
  display:flex;
}

/* Sidebar */
.sidebar {
  width:250px;
  background:rgba(24,26,38,.95);
  border-right:1px solid rgba(255,255,255,.08);
  backdrop-filter:blur(12px);
  box-shadow:3px 0 25px rgba(0,0,0,.35);
  height:100vh;
  position:fixed;
  top:0;left:0;
  display:flex;
  flex-direction:column;
  padding:24px 0;
}

.sidebar h2 {
  color:var(--accent);
  font-weight:800;
  text-align:center;
  margin-bottom:32px;
  letter-spacing:0.5px;
}

.sidebar a {
  color:#dce2f5;
  text-decoration:none;
  display:block;
  padding:12px 32px;
  font-weight:600;
  transition:0.3s;
}

.sidebar a:hover, .sidebar a.active {
  background:linear-gradient(90deg,var(--accent-dark),var(--accent));
  color:#fff;
}

/* Main Content */
.main {
  margin-left:250px;
  flex:1;
  padding:32px;
}

h3.section-title {
  font-weight:700;
  color:var(--accent);
  border-bottom:1px solid rgba(255,255,255,.1);
  padding-bottom:8px;
  margin-bottom:18px;
}

/* Dashboard cards */
.dashboard-cards {
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
  gap:20px;
  margin-bottom:24px;
}

.card-tile {
  background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.08);
  border-radius:14px;
  padding:28px;
  text-align:center;
  font-weight:700;
  cursor:pointer;
  transition:.3s;
  box-shadow:0 8px 25px rgba(0,0,0,.25);
}
.card-tile:hover {
  transform:translateY(-4px);
  box-shadow:0 14px 36px rgba(59,130,246,.3);
  border-color:rgba(59,130,246,.4);
}
.card-tile h5 { color:#fff; }

/* Tables */
/* Product Table - Light Gray for Readability */
.table-container {
  max-height: 500px;
  overflow-y: auto;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
  background: #f5f6fa; /* Light gray background to improve contrast */
}

/* Table Layout */
.table {
  color: #1c1c1c;
  margin-bottom: 0;
  border-collapse: collapse;
  background-color: #ffffff; /* consistent light base */
  border-radius: 10px;
}

/* Sticky header */
.table thead {
  background: #2563eb; /* Blue header for Gym POS accent */
  color: white;
  position: sticky;
  top: 0;
  z-index: 2;
}

/* Table Rows */
.table tbody tr {
  background-color: #ffffff;
  transition: background-color 0.2s ease;
}

.table tbody tr:nth-child(even) {
  background-color: #f0f2f5; /* Light gray alternate row */
}

.table tbody tr:hover {
  background-color: #e0e7ff; /* subtle hover blue tint */
}

/* Table Borders */
.table td,
.table th {
  border-color: rgba(0, 0, 0, 0.1) !important;
  vertical-align: middle;
}

/* Buttons */
.table .btn-warning {
  background-color: #facc15;
  color: #000;
  border: none;
}
.table .btn-warning:hover {
  background-color: #eab308;
}
.table .btn-danger {
  background-color: #ef4444;
  border: none;
}
.table .btn-danger:hover {
  background-color: #dc2626;
}


/* Scrollbar styling */
.table-container::-webkit-scrollbar { width:8px; }
.table-container::-webkit-scrollbar-thumb {
  background-color:rgba(59,130,246,0.6);
  border-radius:10px;
}
.table-container::-webkit-scrollbar-thumb:hover {
  background-color:rgba(59,130,246,0.9);
}

.table {
  color:#e8ecf4;
  margin-bottom:0;
}
.table thead {
  background:rgba(59,130,246,0.15);
  color:#9fc5ff;
  position:sticky;
  top:0;
  z-index:2;
}
.table td, .table th {
  border-color:rgba(255,255,255,.08)!important;
  vertical-align:middle;
}
.table-striped>tbody>tr:nth-of-type(odd)>* {
  background-color:rgba(255,255,255,.04)!important;
}

/* Buttons */
.btn-primary {
  background:linear-gradient(90deg,var(--accent-dark),var(--accent));
  border:0;
  font-weight:700;
  transition:.25s;
}
.btn-primary:hover {
  background:linear-gradient(90deg,var(--accent-deep),var(--accent-dark));
  transform:translateY(-1px);
}
.btn-danger {
  background:#ef4444;border:0;
}
.btn-warning {background:#facc15;border:0;color:#000;}

/* Chart Container */
#salesReport {
  background:rgba(255,255,255,.04);
  border-radius:14px;
  padding:20px;
  box-shadow:0 4px 20px rgba(0,0,0,.3);
}

/* Status Indicator */
.status-indicator {
  display:inline-block;width:12px;height:12px;border-radius:50%;
  margin-right:8px;
}
.active {background:#22c55e;}
.inactive {background:#ef4444;}
</style>
</head>
<body>

<div class="sidebar">
  <h2>Gym POS</h2>
  <a href="#" class="active" onclick="showSection('productList')">🏋️ Products</a>
  <a href="#" onclick="showSection('salesReport')">📈 Sales Report</a>
  <a href="#" onclick="showSection('userInfo')">👤 User Info</a>
  <a href="../actions/logout.php">🚪 Logout</a>
</div>

<div class="main">
  <h3 class="section-title">Admin Dashboard</h3>

  <!-- Quick Cards -->
  <div class="dashboard-cards">
    <div class="card-tile" onclick="showSection('salesReport')">
      <h5>Sales Report</h5>
      <p class="text-muted">View revenue analytics</p>
    </div>
    <div class="card-tile" onclick="showSection('productList')">
      <h5>Product List</h5>
      <p class="text-muted">Manage all gym products</p>
    </div>
    <div class="card-tile" onclick="showSection('userInfo')">
      <h5>User Info</h5>
      <p class="text-muted">Monitor admin activity</p>
    </div>
  </div>

  <!-- Sales Report -->
  <div id="salesReport" class="d-none">
    <h3 class="section-title">Sales Report</h3>
    <canvas id="salesChart" height="120"></canvas>
  </div>

  <!-- Product List -->
  <div id="productList" class="d-none">
    <h3 class="section-title">Product List</h3>
    <button class="btn btn-primary mb-3" onclick="window.location.href='add-product.php'">➕ Add Product</button>

    <div class="mb-3">
      <label for="categoryFilter" class="fw-bold">Filter by Category:</label>
      <select id="categoryFilter" class="form-select w-auto d-inline-block ms-2" onchange="filterCategory()">
        <option value="all">All Categories</option>
        <?php 
        $categories = $product->getCategories();
        foreach ($categories as $category) { 
          echo '<option value="'.htmlspecialchars($category['category']).'">'.htmlspecialchars($category['category']).'</option>'; 
        }
        ?>
      </select>
    </div>

    <!-- Scrollable Table -->
    <div class="table-container">
      <table class="table table-striped text-center align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Category</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="productTableBody">
          <?php foreach ($product_list as $item): ?>
          <tr data-category="<?= htmlspecialchars($item['category']) ?>">
            <td><?= htmlspecialchars($item['product_id']) ?></td>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td>₱<?= htmlspecialchars($item['price']) ?></td>
            <td><?= htmlspecialchars($item['quantity']) ?></td>
            <td><?= htmlspecialchars($item['category']) ?></td>
            <td>
              <a href="edit-product1.php?id=<?= $item['product_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
              <a href="delete-product.php?id=<?= $item['product_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- User Info -->
  <div id="userInfo" class="d-none">
    <h3 class="section-title">User Info</h3>
    <table class="table table-striped text-center">
      <thead>
        <tr>
          <th>Status</th>
          <th>Username</th>
          <th>Login Time</th>
          <th>Logout Time</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><span class="status-indicator <?= $isActive ? 'active' : 'inactive' ?>"></span></td>
          <td><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Unknown' ?></td>
          <td><?= $_SESSION['login_time'] ?></td>
          <td><?= $_SESSION['logout_time'] ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
const salesData = <?= json_encode($sales_data) ?>;
const labels = salesData.map(sale => sale.sale_date);
const data = salesData.map(sale => parseFloat(sale.total_sales));

const ctx = document.getElementById('salesChart');
if (ctx) {
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Total Sales (₱)',
        data: data,
        backgroundColor: 'rgba(59,130,246,0.6)',
        borderColor: 'rgba(59,130,246,1)',
        borderWidth: 1,
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true, ticks:{color:'#cfd6e6'} },
                x: { ticks:{color:'#cfd6e6'} } },
      plugins:{ legend:{ labels:{color:'#cfd6e6'} } }
    }
  });
}

// Section toggle
function showSection(id){
  document.querySelectorAll('#salesReport,#productList,#userInfo').forEach(sec => sec.classList.add('d-none'));
  document.getElementById(id).classList.remove('d-none');
}

// Show product list default
window.onload = ()=>showSection('productList');

// Filter by category
function filterCategory(){
  const selected=document.getElementById("categoryFilter").value;
  document.querySelectorAll("#productTableBody tr").forEach(row=>{
    const cat=row.getAttribute("data-category");
    row.style.display=(selected==="all"||cat===selected)?"":"none";
  });
}
</script>
</body>
</html>
