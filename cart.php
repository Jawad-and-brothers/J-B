<?php
$pageTitle = 'My Cart';
require_once 'config.php';
requireLogin();
$db = getDB();
$user_id = (int)$_SESSION['user_id'];

// Fetch cart items
$cartItems = $db->query("
    SELECT c.quantity, p.id, p.name, p.price, p.image, p.stock, c.quantity * p.price as subtotal
    FROM cart c JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
    ORDER BY c.added_at DESC
");

$cartArr = [];
$cartTotal = 0;
while ($item = $cartItems->fetch_assoc()) {
    $cartArr[] = $item;
    $cartTotal += $item['subtotal'];
}

$shipping = $cartTotal >= 5000 ? 0 : 250;
$grandTotal = $cartTotal + $shipping;
?>
<?php include 'includes/header.php'; ?>

<div class="page-header">
    <div class="container">
        <h2>My Shopping Cart</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Cart</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if (empty($cartArr)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart" style="font-size:80px;color:#ddd;"></i>
            <h3 class="mt-3 text-muted">Your cart is empty</h3>
            <p class="text-muted mb-4">Browse our collection and add items you love.</p>
            <a href="shop.php" class="btn-gold d-inline-block px-5 py-3" style="text-decoration:none;border-radius:8px;font-size:15px;width:auto;">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="bg-white rounded-3 shadow-sm overflow-hidden">
                    <table class="table cart-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:80px;">Image</th>
                                <th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartArr as $item): ?>
                            <tr>
                                <td><a href="product.php?id=<?= $item['id'] ?>"><img src="images/products/<?= sanitize($item['image']) ?>" alt="<?= sanitize($item['name']) ?>"></a></td>
                                <td>
                                    <a href="product.php?id=<?= $item['id'] ?>" style="font-weight:600;color:var(--text-dark);"><?= sanitize($item['name']) ?></a>
                                    <div style="font-size:13px;color:var(--text-light);">Unit price: <?= formatPrice($item['price']) ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="qty-ctrl justify-content-center">
                                        <button class="qty-down" data-id="<?= $item['id'] ?>">−</button>
                                        <input type="number" class="qty-update" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" data-id="<?= $item['id'] ?>">
                                        <button class="qty-up" data-id="<?= $item['id'] ?>">+</button>
                                    </div>
                                </td>
                                <td class="text-end fw-bold" id="subtotal_<?= $item['id'] ?>"><?= formatPrice($item['subtotal']) ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger btn-remove-cart" data-id="<?= $item['id'] ?>" title="Remove">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-3 flex-wrap gap-2">
                    <a href="shop.php" class="btn btn-outline-secondary px-4"><i class="fas fa-arrow-left me-2"></i>Continue Shopping</a>
                    <button onclick="location.reload()" class="btn btn-outline-secondary px-4"><i class="fas fa-sync-alt me-2"></i>Update Cart</button>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h5 style="font-size:1.1rem;margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid var(--green-pale);">Order Summary</h5>
                    <div class="cart-summary-row">
                        <span>Subtotal (<?= count($cartArr) ?> items)</span>
                        <span class="cart-total-display"><?= formatPrice($cartTotal) ?></span>
                    </div>
                    <div class="cart-summary-row">
                        <span>Shipping</span>
                        <span><?= $shipping == 0 ? '<span style="color:var(--green-primary);">FREE</span>' : formatPrice($shipping) ?></span>
                    </div>
                    <?php if ($shipping > 0): ?>
                    <div style="font-size:12px;color:#888;padding:5px 0;border-bottom:1px solid var(--border);">
                        <i class="fas fa-info-circle me-1"></i>Add PKR <?= number_format(5000 - $cartTotal) ?> more for free shipping
                    </div>
                    <?php endif; ?>
                    <div class="cart-summary-row" style="margin-top:5px;">
                        <span>Total</span>
                        <span style="color:var(--green-primary);"><?= formatPrice($grandTotal) ?></span>
                    </div>
                    <a href="checkout.php" class="btn-gold d-block text-center mt-4 py-3" style="text-decoration:none;border-radius:8px;font-size:15px;">
                        <i class="fas fa-lock me-2"></i>Proceed to Checkout
                    </a>
                    <div class="text-center mt-3" style="font-size:12px;color:#888;">
                        <i class="fas fa-shield-alt me-1"></i>Secure checkout · Cash on delivery available
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>