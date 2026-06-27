<?php
// logout.php
session_start(); // Session start karein

// Saari session variables hata dein
$_SESSION = array();

// Agar session cookie use ho rahi hai toh usko bhi destroy karein
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Session ko completely destroy karein
session_destroy();

// User ko login page ya home page par bhejein
header("Location: login.php");
exit;
?>