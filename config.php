<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Change to your MySQL username
define('DB_PASS', '');           // Change to your MySQL password
define('DB_NAME', 'jnb_store');

// Site Configuration
define('SITE_NAME', 'J&B - Jawad & Brothers');
define('SITE_URL', 'http://localhost/J&B'); // Change to your domain
define('CURRENCY', 'PKR');

// Create database connection
function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die('<div style="padding:20px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;margin:20px;border-radius:5px;">
                <strong>Database Connection Failed:</strong> ' . $conn->connect_error . '<br>
                Please ensure MySQL is running and update config.php with your credentials.
                </div>');
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper: Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Helper: Require login (redirect if not)
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php?msg=login_required');
        exit;
    }
}

// Helper: Get cart item count
function getCartCount() {
    if (!isLoggedIn()) return 0;
    $db = getDB();
    $uid = (int)$_SESSION['user_id'];
    $r = $db->query("SELECT SUM(quantity) as total FROM cart WHERE user_id=$uid");
    $row = $r->fetch_assoc();
    return (int)($row['total'] ?? 0);
}

// Helper: Format price
function formatPrice($amount) {
    return 'PKR ' . number_format($amount, 0);
}

// Helper: Sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Helper: Generate order number
function generateOrderNumber() {
    return 'JNB-' . strtoupper(date('ymd')) . '-' . rand(1000, 9999);
}
?>