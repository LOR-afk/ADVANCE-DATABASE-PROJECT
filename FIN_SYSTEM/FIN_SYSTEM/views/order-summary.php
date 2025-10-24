<?php 
session_start();
include "../classes/Product.php";

$product = new Product;

// Initialize order details
$order_details = [];
$total_price = 0;

// Build order from session
if (isset($_SESSION['orders']) && !empty($_SESSION['orders'])) {
    foreach ($_SESSION['orders'] as $product_id => $quantity) {
        if ($quantity > 0) {
            $product_info = $product->displaySpecificProduct($product_id);
            if ($product_info) {
                $product_info['quantity']    = $quantity;
                $product_info['total_price'] = $quantity * $product_info['price'];
                $order_details[]             = $product_info;
                $total_price                += $product_info['total_price'];
            }
        }
    }
}

// Confirm payment
if (isset($_POST['confirm_payment'])) {
    $_SESSION['order_details'] = $order_details;
    $_SESSION['total_price']   = $total_price;
    $_SESSION['payment_method']= 'cash';
    unset($_SESSION['orders']);
    header("Location: sales_receipt.php");
    exit;
}

// Back
if (isset($_POST['back_to_menu'])) {
    header("Location: cashier.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Review Order</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"/>

  <style>
    :root{
      --accent:#3b82f6;
      --accent-dark:#2563eb;
      --accent-deeper:#1e40af;
      --bg1:#0f111a;
      --bg2:#1b1d2a;
      --card: rgba(24,26,38,.92);
      --card-border: rgba(255,255,255,.10);
      --muted:#a9b3c7;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:"Inter",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      background: radial-gradient(1200px 600px at 50% -10%, var(--bg2), var(--bg1));
      color:#e8ecf4;
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:24px;
    }

    /* Glass card */
    .order-card{
      width:100%;
      max-width:1000px;
      background: var(--card);
      border:1px solid var(--card-border);
      border-radius:20px;
      backdrop-filter: blur(12px);
      box-shadow: 0 16px 48px rgba(0,0,0,.45);
      padding:28px;
    }

    .title{
      font-weight:800;
      color:var(--accent);
      letter-spacing:.3px;
      text-align:center;
      margin-bottom:6px;
    }
    .subtitle{
      text-align:center;
      color:var(--muted);
      margin-bottom:20px;
    }

    /* Table */
    .table {
      color: #ffffff !important;
      border-color: rgba(255, 255, 255, 0.12);
      background: transparent;
      margin: 0;
    }

    .table thead th {
      background: rgba(255, 255, 255, 0.06);
      border-bottom: 1px solid rgba(255, 255, 255, 0.12);
      color: #cfe0ff;
      font-weight: 700;
    }

    .table tbody tr {
      background-color: rgba(255, 255, 255, 0.03) !important;
    }

    .table tbody td {
      color: #ffffff !important;
      border-color: rgba(255, 255, 255, 0.08);
      vertical-align: middle;
    }

    .table-striped > tbody > tr:nth-of-type(odd) > * {
      background-color: rgba(255, 255, 255, 0.05) !important;
      color: #ffffff !important;
    }

    /* Totals row */
    .total-wrap{
      display:flex; justify-content:flex-end; margin-top:16px;
    }
    .total-box{
      min-width: 320px;
      background: rgba(255,255,255,.04);
      border:1px solid var(--card-border);
      border-radius:14px;
      padding:14px 16px;
      display:flex; align-items:center; justify-content:space-between;
      font-weight:700;
    }
    .total-box .label{ color:#cfe0ff; }
    .total-box .value{ color:#ffffff; font-size:1.25rem; }

    /* Payment box */
    .payment-box{
      margin-top:18px;
      background: rgba(255,255,255,.04);
      border:1px solid var(--card-border);
      border-radius:14px;
      padding:18px;
      text-align:center;
    }
    .pay-method{
      color:#22c55e;
      font-weight:800;
      font-size:1.25rem;
      margin:8px 0 0;
    }

    /* Buttons */
    .btn{
      border-radius:10px;
      padding:12px 16px;
      font-weight:700;
      letter-spacing:.3px;
      transition:.25s ease;
    }
    .btn-primary{
      background: linear-gradient(90deg, var(--accent-dark), var(--accent));
      border:0;
      box-shadow: 0 8px 26px rgba(59,130,246,.35);
    }
    .btn-primary:hover{
      background: linear-gradient(90deg, var(--accent-deeper), var(--accent-dark));
      transform: translateY(-1px);
      box-shadow: 0 10px 30px rgba(59,130,246,.5);
    }
    .btn-outline{
      background: transparent;
      color:#d9e2f1;
      border:1px solid rgba(255,255,255,.18);
    }
    .btn-outline:hover{
      background: rgba(255,255,255,.06);
      border-color: rgba(255,255,255,.26);
    }

    .actions{
      display:flex;
      gap:12px;
      justify-content:center;
      margin-top:18px;
      flex-wrap:wrap;
    }

    @media (max-width: 640px){
      .total-wrap{ justify-content:stretch; }
      .total-box{ width:100%; }
    }
  </style>
</head>
<body>
  <div class="order-card">
    <h2 class="title">Review Your Order</h2>
    <p class="subtitle">Please confirm the items and total before proceeding to payment.</p>

    <div class="table-responsive">
      <table class="table table-striped table-bordered text-center align-middle">
        <thead>
          <tr>
            <th>Item Description</th>
            <th style="width:120px">Quantity</th>
            <th style="width:150px">Price</th>
            <th style="width:170px">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($order_details)) { ?>
            <?php foreach ($order_details as $item) { ?>
              <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
                <td>₱<?= number_format($item['price'], 2) ?></td>
                <td><strong>₱<?= number_format($item['total_price'], 2) ?></strong></td>
              </tr>
            <?php } ?>
          <?php } else { ?>
            <tr>
              <td colspan="4" class="text-center text-warning">No items selected.</td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <div class="total-wrap">
      <div class="total-box">
        <span class="label">Total</span>
        <span class="value">₱<?= number_format($total_price, 2) ?></span>
      </div>
    </div>

    <div class="payment-box">
      <h5 class="mb-1">Payment Method</h5>
      <div class="pay-method">Cash</div>

      <form method="post" class="actions">
        <input type="hidden" name="payment_method" value="cash">
        <button class="btn btn-primary px-4" name="confirm_payment" type="submit">
          Proceed to Payment
        </button>
        <button class="btn btn-outline px-4" name="back_to_menu" type="submit">
          Cancel / Go Back
        </button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
