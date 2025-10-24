<?php
session_start();

// Optional flash messages
$flash_error = $_SESSION['flash_error'] ?? ($_GET['error'] ?? '');
$flash_ok    = $_SESSION['flash_ok']    ?? ($_GET['ok'] ?? '');
unset($_SESSION['flash_error'], $_SESSION['flash_ok']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Gym POS</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>

  <style>
:root {
  --accent:#3b82f6;
  --accent-dark:#2563eb;
  --accent-deep:#1e40af;
  --bg1:#0f111a;
  --bg2:#1b1d2a;
}
*{box-sizing:border-box}
body{
  font-family:"Inter",sans-serif;
  background:radial-gradient(1200px 600px at 50% -10%,var(--bg2),var(--bg1));
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#e8ecf4;
  padding:24px;
}

/* Auth Card */
.auth-card{
  width:100%;
  max-width:980px;
  background:rgba(24,26,38,.92);
  border:1px solid rgba(255,255,255,.08);
  border-radius:20px;
  overflow:hidden;
  backdrop-filter:blur(12px);
  box-shadow:0 12px 40px rgba(0,0,0,.45);
  display:grid;
  grid-template-columns:1.05fr 1fr;
}

/* Left Side */
.auth-hero{
  background:linear-gradient(180deg,rgba(59,130,246,.08),rgba(37,99,235,.06));
  border-right:1px solid rgba(255,255,255,.06);
  padding:42px 36px;
  display:flex;
  flex-direction:column;
  justify-content:center;
  gap:18px;
}
.brand{display:flex;align-items:center;gap:12px;}
.brand img{width:44px;height:44px;object-fit:contain;filter:drop-shadow(0 2px 8px rgba(59,130,246,.35));}
.brand span{font-weight:800;letter-spacing:.3px;color:#fff;}
.auth-hero h2{font-weight:700;color:var(--accent);}
.auth-hero p{color:#cfd6e6;margin:0;line-height:1.6;}

/* Right Form */
.auth-form{padding:42px 36px;}
.auth-title{text-align:center;color:var(--accent);font-weight:700;margin-bottom:18px;letter-spacing:.4px;}
label{font-weight:600;color:#cfd6e6;}
.form-control{
  background:rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.1);
  color:#fff;
  padding:12px 14px;
  border-radius:10px;
}
.form-control:focus{
  background:rgba(255,255,255,.1);
  border-color:var(--accent);
  box-shadow:0 0 0 .25rem rgba(59,130,246,.25);
  color:#fff;
}
.form-control::placeholder{color:#a9b3c7;}

/* Buttons */
.btn{
  padding:12px 16px;
  border-radius:10px;
  font-weight:600;
  letter-spacing:.4px;
  transition:.25s ease;
}
.btn-primary{
  background:linear-gradient(90deg,var(--accent-dark),var(--accent));
  border:0;
  box-shadow:0 6px 22px rgba(59,130,246,.35);
}
.btn-primary:hover{
  background:linear-gradient(90deg,var(--accent-deep),var(--accent-dark));
  transform:translateY(-1px);
  box-shadow:0 8px 28px rgba(59,130,246,.5);
}
.btn-outline{
  background:transparent;
  color:#d9e2f1;
  border:1px solid rgba(255,255,255,.16);
}
.btn-outline:hover{
  background:rgba(255,255,255,.06);
  border-color:rgba(255,255,255,.24);
}

/* Input Eye Icon */
.input-icon{position:relative;}
.input-icon input.form-control{padding-right:44px !important;height:48px;line-height:48px;}
.input-icon button{
  position:absolute;right:10px;top:50%;transform:translateY(-50%);
  border:none;background:transparent;color:#cfd6e6;cursor:pointer;
  width:32px;height:32px;display:flex;align-items:center;justify-content:center;
}

/* Toasts */
.toast-container{position:fixed;top:18px;right:18px;z-index:1080;}
.toast{
  border-radius:12px;
  background:rgba(24,26,38,.96);
  color:#e8ecf4;
  border:1px solid rgba(255,255,255,.08);
  box-shadow:0 10px 30px rgba(0,0,0,.35);
}
.toast .toast-header{background:rgba(255,255,255,.06);border-bottom:1px solid rgba(255,255,255,.08);color:#e8ecf4;}
.toast-success .indicator{background:#22c55e;width:10px;height:10px;border-radius:50%;margin-right:8px;}
.toast-error .indicator{background:#ef4444;width:10px;height:10px;border-radius:50%;margin-right:8px;}

/* Modal (Create Account) */
.modal-content{
  background:rgba(24,26,38,.95)!important;
  border:1px solid rgba(255,255,255,.08);
  border-radius:18px;
  backdrop-filter:blur(18px);
  color:#e8ecf4;
}
.modal-header{
  background:rgba(255,255,255,.05);
  border-bottom:1px solid rgba(255,255,255,.08);
  color:var(--accent);
  font-weight:700;
}
.modal-body label{color:#cfd6e6;font-weight:600;}
.modal-body .form-control{
  background:rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.1);
  color:#fff;
  padding:12px 14px;
  border-radius:10px;
  margin-bottom:12px;
}
.modal-body .form-control:focus{
  background:rgba(255,255,255,.1);
  border-color:var(--accent);
  box-shadow:0 0 0 .25rem rgba(59,130,246,.25);
}
.modal-body .btn-primary{
  background:linear-gradient(90deg,var(--accent-dark),var(--accent));
  border:0;
  box-shadow:0 6px 22px rgba(59,130,246,.35);
}
.modal-body .btn-primary:hover{
  background:linear-gradient(90deg,var(--accent-deep),var(--accent-dark));
  box-shadow:0 8px 28px rgba(59,130,246,.55);
  transform:translateY(-1px);
}
.btn-close-white{filter:invert(1) brightness(1.8);}
.modal-body .input-icon button{
  right:12px;top:50%;transform:translateY(-50%);
  background:transparent;border:none;color:#cfd6e6;cursor:pointer;
}

@media(max-width:900px){
  .auth-card{grid-template-columns:1fr;}
  .auth-hero{display:none;}
}
  </style>
</head>
<body>

<div class="toast-container">
<?php if(!empty($flash_ok)): ?>
  <div class="toast align-items-center toast-success" role="alert" id="toast-ok">
    <div class="toast-header"><span class="indicator"></span><strong class="me-auto">Success</strong><small>Now</small>
      <button type="button" class="btn-close btn-close-white ms-2 mb-1" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body"><?= htmlspecialchars($flash_ok) ?></div>
  </div>
<?php endif; ?>
<?php if(!empty($flash_error)): ?>
  <div class="toast align-items-center toast-error" role="alert" id="toast-error">
    <div class="toast-header"><span class="indicator"></span><strong class="me-auto">Error</strong><small>Now</small>
      <button type="button" class="btn-close btn-close-white ms-2 mb-1" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body"><?= htmlspecialchars($flash_error) ?></div>
  </div>
<?php endif; ?>
</div>

<div class="auth-card">
  <div class="auth-hero">
    <div class="brand">
      <img src="../assets/logo.svg" alt="Brand Logo" onerror="this.src='https://dummyimage.com/44x44/1f2937/ffffff&text=GYM'">
      <span>Limitless Gym</span>
    </div>
    <h2>Welcome back</h2>
    <p>Sign in to manage products, track sales, and oversee your system seamlessly.</p>
    <p class="text-secondary">Tip: Keep your credentials secure.</p>
  </div>

  <div class="auth-form">
    <h3 class="auth-title">Login</h3>
    <form action="../actions/user-actions.php" method="post">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
      </div>
      <div class="mb-3 input-icon">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
        <button type="button" onclick="togglePassword('password')">&#128065;</button>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary" name="login">Sign In</button>
        <button type="button" class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#registration">
          Create an Account
        </button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="registration" tabindex="-1" aria-labelledby="registrationLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="registrationLabel">Create Account</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="../actions/user-actions.php" method="post">
          <div class="mb-3">
            <label for="first-name" class="form-label">First Name</label>
            <input type="text" name="first_name" id="first-name" class="form-control" placeholder="First name" required>
          </div>
          <div class="mb-3">
            <label for="last-name" class="form-label">Last Name</label>
            <input type="text" name="last_name" id="last-name" class="form-control" placeholder="Last name" required>
          </div>
          <div class="mb-3">
            <label for="reg-username" class="form-label">Username</label>
            <input type="text" name="username" id="reg-username" class="form-control" placeholder="Create a username" required>
          </div>
          <div class="mb-3 input-icon">
            <label for="reg-password" class="form-label">Password</label>
            <input type="password" name="password" id="reg-password" class="form-control" placeholder="Create a password" required>
            <button type="button" onclick="togglePassword('reg-password')">&#128065;</button>
          </div>
          <button type="submit" class="btn btn-primary w-100" name="register">Register</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword(id){
  const input=document.getElementById(id);
  input.type=input.type==="password"?"text":"password";
}
document.addEventListener("DOMContentLoaded",()=>{
  const ok=document.getElementById("toast-ok");
  const err=document.getElementById("toast-error");
  if(ok) new bootstrap.Toast(ok,{delay:4000}).show();
  if(err) new bootstrap.Toast(err,{delay:5000}).show();
});
</script>
</body>
</html>
