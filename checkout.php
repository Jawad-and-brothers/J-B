<?php
$pageTitle = 'Checkout';
require_once 'config.php';
requireLogin();
$db = getDB();
$user_id = (int)$_SESSION['user_id'];

// Fetch cart
$cartItems = $db->query("
    SELECT c.quantity, p.id, p.name, p.price, p.image, p.stock, c.quantity * p.price as subtotal
    FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id
");
$cartArr = [];
$cartTotal = 0;
while ($item = $cartItems->fetch_assoc()) { $cartArr[] = $item; $cartTotal += $item['subtotal']; }

if (empty($cartArr)) { header('Location: cart.php'); exit; }

$shipping = $cartTotal >= 5000 ? 0 : 250;
$grandTotal = $cartTotal + $shipping;

// Fetch user info for prefill
$user = $db->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['ch_name'] ?? '');
    $email = trim($_POST['ch_email'] ?? '');
    $phone = trim($_POST['ch_phone'] ?? '');
    $address = trim($_POST['ch_address'] ?? '');
    $city = trim($_POST['ch_city'] ?? '');
    $payment = sanitize($_POST['payment_method'] ?? 'cod');
    $notes = trim($_POST['notes'] ?? '');

    if (!$name) $errors['ch_name'] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['ch_email'] = 'Valid email is required.';
    if (!preg_match('/^[0-9+\-\s]{10,15}$/', $phone)) $errors['ch_phone'] = 'Valid phone number is required.';
    if (!$address) $errors['ch_address'] = 'Delivery address is required.';
    if (!$city) $errors['ch_city'] = 'City is required.';

    if (empty($errors)) {
        // Re-validate stock
        foreach ($cartArr as $item) {
            if ($item['quantity'] > $item['stock']) {
                $errors['stock'] = "Sorry, '{$item['name']}' has insufficient stock.";
                break;
            }
        }
    }

    if (empty($errors)) {
        $order_number = generateOrderNumber();

        $stmt = $db->prepare("INSERT INTO orders (user_id, order_number, total_amount, shipping_name, shipping_email, shipping_phone, shipping_address, shipping_city, payment_method, notes) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('isdsssssss', $user_id, $order_number, $grandTotal, $name, $email, $phone, $address, $city, $payment, $notes);

        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;

            // Insert order items & reduce stock
            foreach ($cartArr as $item) {
                $iStmt = $db->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price, subtotal) VALUES (?,?,?,?,?,?)");
                $iStmt->bind_param('iisids', $order_id, $item['id'], $item['name'], $item['quantity'], $item['price'], $item['subtotal']);
                $iStmt->execute();
                $db->query("UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['id']}");
            }

            // Clear cart
            $db->query("DELETE FROM cart WHERE user_id=$user_id");

            // Redirect to success
            $_SESSION['last_order_id'] = $order_id;
            $_SESSION['last_order_number'] = $order_number;
            header('Location: order_success.php');
            exit;
        } else {
            $errors['general'] = 'Order placement failed. Please try again.';
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="page-header">
    <div class="container">
        <h2>Checkout</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= !empty($errors['general']) ? $errors['general'] : 'Please correct the errors below.' ?>
        </div>
        <?php endif; ?>

        <form id="checkoutForm" method="POST" action="checkout.php" novalidate>
            <div class="row g-4">
                <!-- Left: Shipping + Payment -->
                <div class="col-lg-7">
                    <!-- Shipping Details -->
                    <div class="checkout-section">
                        <h5><i class="fas fa-map-marker-alt me-2 text-success"></i>Delivery Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control <?= isset($errors['ch_name'])?'is-invalid':'' ?>"
                                    id="ch_name" name="ch_name" value="<?= sanitize($_POST['ch_name'] ?? $user['full_name']) ?>" required>
                                <?php if (isset($errors['ch_name'])): ?><div class="invalid-feedback"><?= $errors['ch_name'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control <?= isset($errors['ch_email'])?'is-invalid':'' ?>"
                                    id="ch_email" name="ch_email" value="<?= sanitize($_POST['ch_email'] ?? $user['email']) ?>" required>
                                <?php if (isset($errors['ch_email'])): ?><div class="invalid-feedback"><?= $errors['ch_email'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <input type="tel" class="form-control <?= isset($errors['ch_phone'])?'is-invalid':'' ?>"
                                    id="ch_phone" name="ch_phone" value="<?= sanitize($_POST['ch_phone'] ?? $user['phone']) ?>" required>
                                <?php if (isset($errors['ch_phone'])): ?><div class="invalid-feedback"><?= $errors['ch_phone'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City *</label>
                                <select class="form-select <?= isset($errors['ch_city'])?'is-invalid':'' ?>" id="ch_city" name="ch_city" required>
                                    <option value="">Select City</option>
                                    <?php foreach(['Karachi','Lahore','Islamabad','Rawalpindi','Peshawar','Quetta','Faisalabad','Multan','Hyderabad','Other'] as $c): ?>
                                    <option value="<?= $c ?>" <?= (($_POST['ch_city'] ?? $user['city']) == $c)?'selected':'' ?>><?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['ch_city'])): ?><div class="invalid-feedback"><?= $errors['ch_city'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Delivery Address *</label>
                                <textarea class="form-control <?= isset($errors['ch_address'])?'is-invalid':'' ?>"
                                    id="ch_address" name="ch_address" rows="2" required placeholder="House/flat number, street, area..."><?= sanitize($_POST['ch_address'] ?? $user['address']) ?></textarea>
                                <?php if (isset($errors['ch_address'])): ?><div class="invalid-feedback"><?= $errors['ch_address'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Order Notes (optional)</label>
                                <textarea class="form-control" name="notes" rows="2" placeholder="Special instructions for your order..."><?= sanitize($_POST['notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-section">
                        <h5><i class="fas fa-credit-card me-2 text-success"></i>Payment Method</h5>
                        <label class="payment-option d-flex align-items-center selected" id="cod_option">
                            <input type="radio" name="payment_method" value="cod" checked onchange="selectPayment('cod')">
                            <div class="ms-3">
                                <div style="font-weight:700;font-size:15px;"><i class="fas fa-money-bill-wave me-2 text-success"></i>Cash on Delivery</div>
                                <div style="font-size:12.5px;color:#888;margin-top:2px;">Pay when your order arrives. Available across Pakistan.</div>
                            </div>
                        </label>
                        <label class="payment-option d-flex align-items-center" id="bank_option">
                            <input type="radio" name="payment_method" value="bank_transfer" onchange="selectPayment('bank_transfer')">
                            <div class="ms-3">
                                <div style="font-weight:700;font-size:15px;"><i class="fas fa-university me-2" style="color:var(--gold);"></i>Bank Transfer</div>
                                <div style="font-size:12.5px;color:#888;margin-top:2px;">Transfer to our account. Order confirmed after payment verification.</div>
                            </div>
                        </label>
                        <label class="payment-option d-flex align-items-center" id="easypaisa_option">
                            <input type="radio" name="payment_method" value="easypaisa" onchange="selectPayment('easypaisa')">
                            <div class="ms-3">
                                <div style="font-weight:700;font-size:15px;"><i class="fas fa-mobile-alt me-2" style="color:#00a651;"></i>EasyPaisa / JazzCash</div>
                                <div style="font-size:12.5px;color:#888;margin-top:2px;">Send payment via EasyPaisa or JazzCash to: 0300-0000000</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Right: Order Summary -->
                <div class="col-lg-5">
                    <div class="checkout-section" style="position:sticky;top:80px;">
                        <h5><i class="fas fa-receipt me-2 text-success"></i>Order Summary</h5>
                        <?php foreach ($cartArr as $item): ?>
                        <div class="order-summary-item">
                            <div class="d-flex align-items-center gap-2">
                                <img src="images/products/<?= sanitize($item['image']) ?>" style="width:45px;height:45px;object-fit:cover;border-radius:6px;" alt="">
                                <div>
                                    <div style="font-size:14px;font-weight:600;"><?= sanitize($item['name']) ?></div>
                                    <div style="font-size:12px;color:#888;">Qty: <?= $item['quantity'] ?></div>
                                </div>
                            </div>
                            <div style="font-weight:600;"><?= formatPrice($item['subtotal']) ?></div>
                        </div>
                        <?php endforeach; ?>

                        <div class="order-summary-item">
                            <span>Subtotal</span>
                            <span><?= formatPrice($cartTotal) ?></span>
                        </div>
                        <div class="order-summary-item">
                            <span>Shipping</span>
                            <span><?= $shipping == 0 ? '<span style="color:green;">FREE</span>' : formatPrice($shipping) ?></span>
                        </div>
                        <div class="order-summary-item" style="font-size:1.1rem;font-weight:700;color:var(--green-dark);">
                            <span>Grand Total</span>
                            <span><?= formatPrice($grandTotal) ?></span>
                        </div>

                        <button type="submit" class="btn-gold mt-4 py-3 d-block w-100 text-center" style="border-radius:8px;font-size:16px;border:none;cursor:pointer;">
                            <i class="fas fa-check-circle me-2"></i>Place Order
                        </button>
                        <p class="text-center mt-3" style="font-size:12px;color:#888;">
                            <i class="fas fa-shield-alt me-1"></i>Your order is protected by J&B's guarantee
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
function selectPayment(method) {
    document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
    document.getElementById(method + '_option')?.classList.add('selected');
}
</script>

<?php include 'includes/footer.php'; ?>