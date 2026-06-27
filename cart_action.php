<?php
require_once '../config.php';
header('Content-Type: application/json');

// Must be logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to manage your cart.', 'redirect' => '../login.php?msg=login_required']);
    exit;
}

$db = getDB();
$action = $_POST['action'] ?? '';
$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));
$user_id = (int)$_SESSION['user_id'];

function cartCount($db, $user_id) {
    $r = $db->query("SELECT SUM(quantity) as total FROM cart WHERE user_id=$user_id");
    return (int)($r->fetch_assoc()['total'] ?? 0);
}

function cartTotal($db, $user_id) {
    $r = $db->query("SELECT SUM(c.quantity * p.price) as total FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
    $total = (float)($r->fetch_assoc()['total'] ?? 0);
    return 'PKR ' . number_format($total, 0);
}

switch ($action) {
    case 'add':
        if (!$product_id) { echo json_encode(['success'=>false,'message'=>'Invalid product.']); exit; }

        // Verify product exists and has stock
        $prod = $db->query("SELECT id, stock FROM products WHERE id=$product_id")->fetch_assoc();
        if (!$prod) { echo json_encode(['success'=>false,'message'=>'Product not found.']); exit; }
        if ($prod['stock'] < 1) { echo json_encode(['success'=>false,'message'=>'Sorry, this product is out of stock.']); exit; }

        // Check existing cart entry
        $existing = $db->query("SELECT quantity FROM cart WHERE user_id=$user_id AND product_id=$product_id")->fetch_assoc();
        if ($existing) {
            $new_qty = $existing['quantity'] + $quantity;
            if ($new_qty > $prod['stock']) $new_qty = $prod['stock'];
            $db->query("UPDATE cart SET quantity=$new_qty WHERE user_id=$user_id AND product_id=$product_id");
        } else {
            $qty = min($quantity, $prod['stock']);
            $db->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $qty)");
        }

        echo json_encode([
            'success' => true,
            'cart_count' => cartCount($db, $user_id),
            'message' => 'Added to cart!'
        ]);
        break;

    case 'update':
        if (!$product_id || $quantity < 1) { echo json_encode(['success'=>false,'message'=>'Invalid input.']); exit; }
        $prod = $db->query("SELECT price, stock FROM products WHERE id=$product_id")->fetch_assoc();
        if (!$prod) { echo json_encode(['success'=>false,'message'=>'Product not found.']); exit; }
        if ($quantity > $prod['stock']) $quantity = $prod['stock'];
        $db->query("UPDATE cart SET quantity=$quantity WHERE user_id=$user_id AND product_id=$product_id");

        $subtotal = $quantity * $prod['price'];
        echo json_encode([
            'success' => true,
            'item_subtotal' => 'PKR ' . number_format($subtotal, 0),
            'cart_total' => cartTotal($db, $user_id),
            'cart_count' => cartCount($db, $user_id)
        ]);
        break;

    case 'remove':
        if (!$product_id) { echo json_encode(['success'=>false,'message'=>'Invalid product.']); exit; }
        $db->query("DELETE FROM cart WHERE user_id=$user_id AND product_id=$product_id");
        echo json_encode([
            'success' => true,
            'cart_count' => cartCount($db, $user_id),
            'cart_total' => cartTotal($db, $user_id)
        ]);
        break;

    case 'clear':
        $db->query("DELETE FROM cart WHERE user_id=$user_id");
        echo json_encode(['success'=>true,'cart_count'=>0]);
        break;

    default:
        echo json_encode(['success'=>false,'message'=>'Invalid action.']);
}
?>