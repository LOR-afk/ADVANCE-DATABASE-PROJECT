<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gym POS - Home</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <!-- Inter font (matches Add Product & Login) -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"/>

  <style>
    :root{
      --accent:#3b82f6;
      --accent-dark:#2563eb;
      --accent-deeper:#1e40af;
      --bg1:#0f111a;
      --bg2:#1b1d2a;
      --card: rgba(24,26,38,.92);
      --card-border: rgba(255,255,255,.08);
      --muted:#a9b3c7;
    }

    *{box-sizing:border-box;}

    body{
      margin:0;
      font-family:"Inter",sans-serif;
      background: radial-gradient(1200px 600px at 50% -10%, var(--bg2), var(--bg1));
      color:#e8ecf4;
      min-height:100vh;
      display:flex;
    }

    /* Sidebar */
    .sidebar{
      width:260px;
      background: rgba(17,19,28,.85);
      backdrop-filter: blur(10px);
      border-right:1px solid var(--card-border);
      box-shadow: 8px 0 24px rgba(0,0,0,.35);
      padding:24px 18px;
      position:sticky;
      top:0;
      height:100vh;
    }

    .brand{
      display:flex;
      align-items:center;
      gap:10px;
      padding:6px 10px;
      margin-bottom:16px;
    }
    .brand .dot{
      width:12px;
      height:12px;
      border-radius:50%;
      background:var(--accent);
      box-shadow:0 0 16px rgba(59,130,246,.7);
    }
    .brand span{
      font-weight:800;
      letter-spacing:.3px;
      color:#fff;
    }

    .nav-links{
      margin-top:10px;
    }

    .nav-links a{
      display:flex;
      align-items:center;
      gap:10px;
      color:#d7deef;
      text-decoration:none;
      padding:12px 12px;
      border-radius:10px;
      transition:.25s ease;
      font-weight:600;
    }

    .nav-links a:hover{
      background: rgba(255,255,255,.08);
      color:#fff;
    }

    .nav-links a.active{
      background: linear-gradient(90deg, var(--accent-deeper), var(--accent-dark));
      color:#fff;
      box-shadow:0 6px 20px rgba(59,130,246,.35);
    }

    .nav-footer{
      position:absolute;
      bottom:16px;
      left:18px;
      right:18px;
      color:var(--muted);
      font-size:.9rem;
    }

    /* Main section */
    .main{
      flex:1;
      padding:40px 32px;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .card-hero{
      width:100%;
      max-width:800px;
      background: var(--card);
      border:1px solid var(--card-border);
      border-radius:20px;
      backdrop-filter: blur(12px);
      box-shadow: 0 12px 40px rgba(0,0,0,.45);
      padding:48px 36px;
      text-align:center;
    }

    .card-hero h1{
      font-weight:800;
      color:#fff;
      margin-bottom:10px;
      letter-spacing:.3px;
    }

    .subtitle{
      color:var(--muted);
      margin-bottom:28px;
      font-size:1rem;
    }

    /* Buttons */
    .btn{
      border-radius:10px;
      padding:12px 16px;
      font-weight:600;
      letter-spacing:.3px;
      transition:.25s ease;
    }

    .btn-primary{
      background: linear-gradient(90deg, var(--accent-dark), var(--accent));
      border:0;
      color:#fff;
      box-shadow: 0 6px 22px rgba(59,130,246,.35);
    }

    .btn-primary:hover{
      background: linear-gradient(90deg, var(--accent-deeper), var(--accent-dark));
      box-shadow: 0 8px 28px rgba(59,130,246,.5);
      transform: translateY(-1px);
    }

    /* Responsive */
    @media (max-width: 900px){
      .sidebar{ display:none; }
      .main{ padding:24px; }
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="brand">
      <div class="dot"></div>
      <span>Gym POS</span>
    </div>

    <nav class="nav-links">
      <a href="home.php" class="active"><i class="fa-solid fa-house"></i> Home</a>
      <a href="../actions/logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>

    <div class="nav-footer">
      <small>Customer Portal • <?= date('Y') ?></small>
    </div>
  </aside>

  <main class="main">
    <section class="card-hero">
      <h1>Welcome to Gym POS 💪</h1>
      <div class="subtitle">
        Ready to order your favorite gym products?  
        Click the button below to start selecting your items.
      </div>

      <a href="cashier.php" class="btn btn-primary">
        <i class="fa-solid fa-cart-shopping"></i> Select Your Products
      </a>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
