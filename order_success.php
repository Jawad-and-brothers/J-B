<?php
$pageTitle = 'Order Confirmed';
require_once 'config.php';
requireLogin();

$order_id = $_SESSION['last_order_id'] ?? null;
$order_number = $_SESSION['last_order_number'] ?? null;

if (!$order_id) { header('Location: index.php'); exit; }

unset($_SESSION['last_order_id'], $_SESSION['last_order_number']);

$db = getDB();
$order = $db->query("SELECT * FROM orders WHERE id=$order_id")->fetch_assoc();
$items = $db->query("SELECT * FROM order_items WHERE order_id=$order_id");
?>
<?php include 'includes/header.php'; ?>

<section class="py-5">
    <div class="container">
        <div class="success-page">
            <div class="success-icon animate-in">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="mb-2" style="color:var(--green-dark);">Order Placed Successfully!</h2>
            <p style="font-size:16px;color:#666;margin-bottom:5px;">Thank you for shopping with J&B Jawad & Brothers.</p>
            <p style="font-size:15px;color:#888;">Your order number is: <strong style="color:var(--green-primary);"><?= sanitize($order_number) ?></strong></p>

            <div class="row justify-content-center mt-4">
                <div class="col-lg-7">
                    <div class="bg-white rounded-3 shadow-sm p-4 text-start">
                        <h5 class="mb-3" style="color:var(--green-dark);">Order Details</h5>
                        <div class="row mb-3" style="font-size:14px;">
                            <div class="col-6"><strong>Order Number:</strong><br><?= sanitize($order['order_number']) ?></div>
                            <div class="col-6"><strong>Date:</strong><br><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></div>
                            <div class="col-6 mt-3"><strong>Payment:</strong><br><?= ucwords(str_replace('_',' ',$order['payment_method'])) ?></div>
                            <div class="col-6 mt-3"><strong>Status:</strong><br><span class="order-status status-pending">Pending</span></div>
                        </div>

                        <hr>
                        <h6 class="mb-3">Delivery To</h6>
                        <p style="font-size:14px;color:#555;margin:0;">
                            <?= sanitize($order['shipping_name']) ?><br>
                            <?= sanitize($order['shipping_address']) ?>, <?= sanitize($order['shipping_city']) ?><br>
                            <?= sanitize($order['shipping_phone']) ?> | <?= sanitize($order['shipping_email']) ?>
                        </p>

                        <hr>
                        <h6 class="mb-3">Items Ordered</h6>
                        <?php while ($item = $items->fetch_assoc()): ?>
                        <div class="d-flex justify-content-between mb-2" style="font-size:14px;">
                            <span><?= sanitize($item['product_name']) ?> × <?= $item['quantity'] ?></span>
                            <strong><?= formatPrice($item['subtotal']) ?></strong>
                        </div>
                        <?php endwhile; ?>
                        <hr>
                        <div class="d-flex justify-content-between" style="font-size:16px;font-weight:700;color:var(--green-dark);">
                            <span>Total Amount</span>
                            <span><?= formatPrice($order['total_amount']) ?></span>
                        </div>
                    </div>

                    <?php if ($order['payment_method'] !== 'cod'): ?>
                    <div class="alert alert-info mt-3 text-start" style="font-size:14px;">
                        <i class="fas fa-info-circle me-2"></i>
                        Please send your payment of <strong><?= formatPrice($order['total_amount']) ?></strong> to confirm your order.
                        We'll process it within 24 hours of payment verification.
                    </div>
                    <?php endif; ?>

                    <div class="d-flex gap-3 justify-content-center mt-4 flex-wrap">
                        <a href="orders.php" class="btn-green px-5 py-3 d-inline-block" style="text-decoration:none;border-radius:8px;font-size:15px;width:auto;">
                            <i class="fas fa-box me-2"></i>My Orders
                        </a>
                        <a href="shop.php" class="btn-gold px-5 py-3 d-inline-block" style="text-decoration:none;border-radius:8px;font-size:15px;width:auto;">
                            <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>