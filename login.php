<?php
require_once 'admin_config.php';
if (isAdminLoggedIn()) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if ($u === ADMIN_USERNAME && $p === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $u;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | J&B</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --green-dark:#1e3a1e; --green-primary:#4a7c59; --gold:#c9a84c; }
        * { box-sizing: border-box; margin:0; padding:0; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #1e3a1e 0%, #2d5a2d 50%, #1a2e1a 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: #fff; border-radius: 16px; padding: 44px 40px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,0.35); }
        .login-logo { text-align: center; margin-bottom: 8px; }
        .login-logo img { height: 70px; }
        h2 { text-align: center; font-family: 'Playfair Display', serif; font-size: 1.6rem; color: #1a1a1a; margin-bottom: 4px; }
        .subtitle { text-align: center; color: #888; font-size: 13.5px; margin-bottom: 28px; }
        .badge-panel { background: linear-gradient(135deg,var(--green-primary),var(--green-dark)); color: #fff; font-size: 11px; font-weight: 600; padding: 4px 14px; border-radius: 50px; letter-spacing: 1px; display: inline-block; margin-bottom: 22px; }
        .form-label { font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px; }
        .form-control { border: 1.5px solid #ddd; border-radius: 8px; padding: 11px 14px; font-size: 14px; }
        .form-control:focus { border-color: var(--green-primary); box-shadow: 0 0 0 3px rgba(74,124,89,0.12); outline: none; }
        .btn-login { background: var(--green-primary); color: #fff; border: none; border-radius: 8px; padding: 13px; width: 100%; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.3s; margin-top: 6px; }
        .btn-login:hover { background: var(--green-dark); }
        .error-box { background: #f8d7da; color: #721c24; border-radius: 8px; padding: 11px 15px; font-size: 13.5px; margin-bottom: 18px; border-left: 4px solid #dc3545; }
        .pw-wrap { position: relative; }
        .pw-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer; font-size: 15px; }
        .back-link { text-align: center; margin-top: 18px; font-size: 13px; color: #888; }
        .back-link a { color: var(--green-primary); font-weight: 600; }
        .default-creds { background: #f0f9f4; border: 1px solid #b7dfca; border-radius: 8px; padding: 10px 14px; font-size: 12px; color: #2d5a2d; margin-bottom: 18px; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-logo">
        <img src="../images/logo.png" alt="J&B Logo">
    </div>
    <div class="text-center mb-1"><span class="badge-panel">ADMIN PANEL</span></div>
    <h2>Welcome Back</h2>
    <p class="subtitle">Sign in to manage your store</p>

    <div class="default-creds">
        <i class="fas fa-info-circle me-1"></i>
        <strong>Default:</strong> Username: <code>admin</code> &nbsp;|&nbsp; Password: <code>Admin@123</code>
    </div>

    <?php if ($error): ?>
    <div class="error-box"><i class="fas fa-exclamation-circle me-2"></i><?= sanitize($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?= sanitize($_POST['username'] ?? '') ?>" placeholder="admin" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="pw-wrap">
                <input type="password" class="form-control" name="password" id="adminPw" placeholder="Your password" required>
                <button type="button" class="pw-toggle" onclick="togglePw()"><i class="fas fa-eye" id="pwIcon"></i></button>
            </div>
        </div>
        <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt me-2"></i>Login to Admin Panel</button>
    </form>
    <div class="back-link"><a href="../index.php"><i class="fas fa-arrow-left me-1"></i>Back to Website</a></div>
</div>
<script>
function togglePw() {
    const input = document.getElementById('adminPw');
    const icon = document.getElementById('pwIcon');
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}
</script>
</body>
</html>