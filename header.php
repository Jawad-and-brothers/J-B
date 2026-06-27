<?php
// require_once 'config.php';
$cartCount = getCartCount();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' : '' ?>J&B - Jawad &amp; Brothers</title>
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="top-bar-left">
                <span><i class="fas fa-phone me-1"></i>+92-345-2729783</span>
                <span class="ms-3"><i class="fas fa-envelope me-1"></i>Ansari.Jawad89@gmail.com</span>
            </div>
            <div class="top-bar-right">
                <?php if (isLoggedIn()): ?>
                    <span class="me-3"><i class="fas fa-user me-1"></i> <?= sanitize($_SESSION['user_name'] ?? 'User') ?></span>
                    <a href="orders.php" class="me-2"><i class="fas fa-box me-1"></i>My Orders</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
                <?php else: ?>
                    <a href="login.php" class="me-3"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                    <a href="register.php"><i class="fas fa-user-plus me-1"></i>Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="images/logo.png" alt="J&B Logo" height="70">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage==='index'?'active':'' ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $currentPage==='shop'?'active':'' ?>" href="#" data-bs-toggle="dropdown">Shop</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="shop.php">All Products</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php
                        $db = getDB();
                        $cats = $db->query("SELECT * FROM categories ORDER BY name");
                        while($cat = $cats->fetch_assoc()):
                        ?>
                        <li><a class="dropdown-item" href="shop.php?cat=<?= $cat['id'] ?>"><?= sanitize($cat['name']) ?></a></li>
                        <?php endwhile; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage==='about'?'active':'' ?>" href="about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage==='contact'?'active':'' ?>" href="./contact.php">Contact</a>
                </li>
            </ul>
            <div class="navbar-actions d-flex align-items-center gap-3">
                <a href="shop.php" class="nav-icon" title="Search"><i class="fas fa-search"></i></a>
                <a href="cart.php" class="nav-icon cart-icon" title="Cart">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cartCount > 0): ?>
                    <span class="cart-badge"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>
                <?php if (isLoggedIn()): ?>
                <a href="account.php" class="nav-icon" title="Account"><i class="fas fa-user-circle"></i></a>
                <?php else: ?>
                <a href="login.php" class="btn btn-primary-green btn-sm">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<?php if (isset($_SESSION['flash_msg'])): ?>
<div class="container mt-3">
    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['flash_msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php unset($_SESSION['flash_msg'], $_SESSION['flash_type']); endif; ?>