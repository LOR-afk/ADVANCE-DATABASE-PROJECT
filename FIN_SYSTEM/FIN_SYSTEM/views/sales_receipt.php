<?php 
session_start();

// Set the default timezone
date_default_timezone_set('Asia/Manila');

// Check if session is empty and redirect if necessary
if (empty($_SESSION['order_details']) || empty($_SESSION['total_price']) || empty($_SESSION['payment_method'])) {
    header("Location: order-summary.php");
    exit;
}

// Retrieve order details and payment method
$order_details = $_SESSION['order_details'];
$total_price = $_SESSION['total_price'];
$payment_method = $_SESSION['payment_method'];

// Generate a unique transaction ID
$transaction_id = uniqid('', true);

// Get current date and time in the desired format
$current_datetime = date('Y-m-d H:i:s');

// Clear the order details and payment method from session
unset($_SESSION['order_details'], $_SESSION['total_price'], $_SESSION['payment_method']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color:rgb(20, 22, 20); }
        .receipt-container {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }
        .btn-container { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="receipt-container" id="receiptContent">
            <h1 class="text-center">Sales Receipt</h1>
            <h5 class="text-center">Payment Method: <?= htmlspecialchars(ucfirst($payment_method)) ?></h5>
            <h5 class="text-center">Transaction ID: <?= htmlspecialchars($transaction_id) ?></h5>
            <h5 class="text-center">Date & Time: <?= htmlspecialchars($current_datetime) ?></h5>
            <hr>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_details as $item) { ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td>₱<?= htmlspecialchars(number_format($item['price'], 2)) ?></td>
                            <td>₱<?= htmlspecialchars(number_format($item['total_price'], 2)) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <h3 class="text-end">Order Total: ₱<?= htmlspecialchars(number_format($total_price, 2)) ?></h3>
            <hr>
            
            <div class="mb-3">
                <label for="paymentAmount" class="form-label">Payment Received Amount: (Press Enter)</label>
                <input type="number" class="form-control" id="paymentAmount" placeholder="Enter received payment amount" />
            </div>
            
            <h4 class="text-end">Payment Received Amount: ₱<span id="paymentAmountDisplay"></span></h4>
            <h4 class="text-end" id="changeDisplay" style="display: none;">Change: ₱<span id="changeAmount"></span></h4>
            <hr>

            <div class="text-center btn-container">
                <a href="home.php" class="btn btn-success">Back to Menu</a>
                <button class="btn btn-primary" id="downloadReceipt">Receipt</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('paymentAmount').addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                const paymentAmount = parseFloat(this.value);
                const totalPrice = parseFloat('<?= $total_price ?>');

                if (isNaN(paymentAmount) || paymentAmount < totalPrice) {
                    alert('Payment is Insufficient. Please enter a valid amount.');
                    this.value = '';
                } else {
                    alert('Payment is Successful!');
                    const change = paymentAmount - totalPrice;
                    document.getElementById('changeAmount').textContent = change.toFixed(2);
                    document.getElementById('changeDisplay').style.display = 'block';
                    document.getElementById('paymentAmountDisplay').textContent = paymentAmount.toFixed(2);
                }
            }
        });

        document.getElementById('downloadReceipt').addEventListener('click', function () {
            const receiptContainer = document.getElementById('receiptContent').cloneNode(true);
            receiptContainer.querySelector('.btn-container').remove();
            
            const newWindow = window.open('', '_blank');
            newWindow.document.write(`
                <html>
                <head>
                    <title>Sales Receipt</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                </head>
                <body>
                    ${receiptContainer.outerHTML}
                </body>
                </html>
            `);
            newWindow.document.close();
            newWindow.print();
        });
    </script>
</body>
</html>
