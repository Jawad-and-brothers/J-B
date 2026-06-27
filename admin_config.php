<?php
require_once '../config.php';

// ---- ADMIN CREDENTIALS (change these!) ----
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'Admin@123'); // Change this password!

// Check admin login
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Get dashboard stats
function getDashboardStats($db) {
    return [
        'total_products' => $db->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'],
        'total_orders'   => $db->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'],
        'total_users'    => $db->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'],
        'total_revenue'  => $db->query("SELECT SUM(total_amount) as c FROM orders WHERE status != 'cancelled'")->fetch_assoc()['c'] ?? 0,
        'pending_orders' => $db->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'],
        'categories'     => $db->query("SELECT COUNT(*) as c FROM categories")->fetch_assoc()['c'],
    ];
}
?>