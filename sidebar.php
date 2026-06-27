<?php $currentAdmin = basename($_SERVER['PHP_SELF'], '.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($adminTitle) ? sanitize($adminTitle) . ' | ' : '' ?>J&B Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/')-2) ?>admin/admin.css">
</head>
<body>
<div class="admin-wrapper">

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="../images/logo.png" alt="J&B">
        <div class="sidebar-brand-text">
            <strong>J&B Admin</strong>
            <span>Control Panel</span>
        </div>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">Main</div>
        <a href="dashboard.php" class="nav-item <?= $currentAdmin==='dashboard'?'active':'' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">Catalog</div>
        <a href="products.php" class="nav-item <?= $currentAdmin==='products'||$currentAdmin==='add_product'||$currentAdmin==='edit_product'?'active':'' ?>">
            <i class="fas fa-tshirt"></i> Products
        </a>
        <a href="categories.php" class="nav-item <?= $currentAdmin==='categories'?'active':'' ?>">
            <i class="fas fa-tags"></i> Categories
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">Sales</div>
        <a href="orders.php" class="nav-item <?= $currentAdmin==='orders'||$currentAdmin==='order_detail'?'active':'' ?>">
            <i class="fas fa-shopping-bag"></i> Orders
            <?php
            $db = getDB();
            $pending = $db->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
            if ($pending > 0): ?><span class="nav-badge"><?= $pending ?></span><?php endif; ?>
        </a>
        <a href="customers.php" class="nav-item <?= $currentAdmin==='customers'?'active':'' ?>">
            <i class="fas fa-users"></i> Customers
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">System</div>
        <a href="../index.php" class="nav-item" target="_blank">
            <i class="fas fa-external-link-alt"></i> View Website
        </a>
        <a href="logout.php" class="nav-item" style="color:rgba(255,100,100,0.8);">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="admin-user-info">
            <div class="admin-avatar">A</div>
            <div>
                <span>Administrator</span><br>
                <small>J&B Admin Panel</small>
            </div>
        </div>
    </div>
</aside>
<!-- END SIDEBAR -->

<div class="main-content">
<!-- TOPBAR -->
<header class="topbar">
    <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    <div class="topbar-title"><?= isset($adminTitle) ? sanitize($adminTitle) : 'Dashboard' ?></div>
    <div class="topbar-actions">
        <a href="orders.php?status=pending" class="topbar-btn" title="Pending Orders">
            <i class="fas fa-bell"></i>
            <?php if ($pending > 0): ?><span class="badge"><?= $pending ?></span><?php endif; ?>
        </a>
        <a href="../index.php" class="topbar-btn" title="View Site" target="_blank"><i class="fas fa-globe"></i></a>
        <a href="logout.php" class="topbar-btn" title="Logout" style="color:#dc3545;"><i class="fas fa-sign-out-alt"></i></a>
    </div>
</header>