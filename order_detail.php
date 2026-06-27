<?php
$adminTitle = 'Order Detail';
require_once 'admin_config.php';
requireAdmin();
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: orders.php'); exit; }

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = sanitize($_POST['status']);
    $allowed_statuses = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($new_status, $allowed_statuses)) {
        $db->query("UPDATE orders SET status='$new_status' WHERE id=$id");
        $_SESSION['admin_msg'] = ['type'=>'success','text'=>'Order status updated to ' . ucfirst($new_status) . '.'];
        header('Location: order_detail.php?id='.$id); exit;
    }
}

$order = $db->query("SELECT o.*, u.full_name, u.email, u.phone FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=$id")->fetch_assoc();
if (!$order) { header('Location: orders.php'); exit; }

$items = $db->query("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$id");

$statusColors = ['pending'=>'badge-pending','processing'=>'badge-processing','shipped'=>'badge-shipped','delivered'=>'badge-delivered','cancelled'=>'badge-cancelled'];
$msg = $_SESSION['admin_msg'] ?? null; unset($_SESSION['admin_msg']);
?>
<?php include 'includes/sidebar.php'; ?>
<div class="page-body">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="orders.php" style="color:var(--text-mid);font-size:18px;"><i class="fas fa-arrow-left"></i></a>
    <h4 style="margin:0;font-size:1.3rem;">Order — <?= sanitize($order['order_number']) ?></h4>
    <span class="badge-status <?= $statusColors[$order['status']] ?? '' ?>"><?= ucfirst($order['status']) ?></span>
</div>

<?php if ($msg): ?>
<div class="alert-admin <?= $msg['type'] ?>"><i class="fas fa-check-circle me-2"></i><?= sanitize($msg['text']) ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left -->
    <div class="col-lg-8">

        <!-- Items -->
        <div class="data-card mb-4">
            <div class="data-card-header"><div class="data-card-title"><i class="fas fa-list me-2 text-success"></i>Order Items</div></div>
            <table class="data-table">
                <thead><tr><th>Image</th><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
                <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?php if ($item['image']): ?><img src="../images/products/<?= sanitize($item['image']) ?>" alt=""><?php else: ?><div style="width:52px;height:52px;background:#f0f0f0;border-radius:8px;"></div><?php endif; ?></td>
                    <td><strong><?= sanitize($item['product_name']) ?></strong></td>
                    <td>PKR <?= number_format($item['price'],0) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><strong>PKR <?= number_format($item['subtotal'],0) ?></strong></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <div style="padding:15px 20px;background:#f8faf8;border-top:2px solid var(--border);text-align:right;">
                <span style="font-size:16px;font-weight:700;color:var(--green-dark);">Total: PKR <?= number_format($order['total_amount'],0) ?></span>
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="data-card">
            <div class="data-card-header"><div class="data-card-title"><i class="fas fa-map-marker-alt me-2 text-success"></i>Delivery Information</div></div>
            <div style="padding:22px;">
                <div class="row g-3">
                    <div class="col-md-4"><div class="order-detail-box"><h6>Customer Name</h6><strong><?= sanitize($order['shipping_name']) ?></strong></div></div>
                    <div class="col-md-4"><div class="order-detail-box"><h6>Email</h6><?= sanitize($order['shipping_email']) ?></div></div>
                    <div class="col-md-4"><div class="order-detail-box"><h6>Phone</h6><?= sanitize($order['shipping_phone']) ?></div></div>
                    <div class="col-md-8"><div class="order-detail-box"><h6>Address</h6><?= sanitize($order['shipping_address']) ?></div></div>
                    <div class="col-md-4"><div class="order-detail-box"><h6>City</h6><?= sanitize($order['shipping_city']) ?></div></div>
                    <?php if ($order['notes']): ?>
                    <div class="col-12"><div class="order-detail-box"><h6>Customer Notes</h6><?= sanitize($order['notes']) ?></div></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Right -->
    <div class="col-lg-4">
        <!-- Update Status -->
        <div class="form-card mb-4">
            <div class="form-section-title"><i class="fas fa-edit me-2"></i>Update Order Status</div>
            <form method="POST">
                <div class="mb-3">
                    <select name="status" class="form-select">
                        <?php foreach(['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="update_status" class="btn-admin-primary w-100">
                    <i class="fas fa-save me-2"></i>Update Status
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="form-card mb-4">
            <div class="form-section-title"><i class="fas fa-receipt me-2"></i>Order Summary</div>
            <div style="font-size:13.5px;">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0;"><span style="color:#888;">Order Number</span><strong><?= sanitize($order['order_number']) ?></strong></div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0;"><span style="color:#888;">Date</span><span><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span></div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0;"><span style="color:#888;">Payment</span><span><?= ucwords(str_replace('_',' ',$order['payment_method'])) ?></span></div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0;"><span style="color:#888;">Status</span><span class="badge-status <?= $statusColors[$order['status']] ?? '' ?>"><?= ucfirst($order['status']) ?></span></div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;font-size:16px;font-weight:700;color:var(--green-dark);"><span>Total</span><span>PKR <?= number_format($order['total_amount'],0) ?></span></div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="form-card">
            <div class="form-section-title"><i class="fas fa-user me-2"></i>Customer</div>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <div style="width:42px;height:42px;background:var(--green-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--green-primary);font-weight:700;font-size:16px;"><?= strtoupper($order['full_name'][0]) ?></div>
                <div>
                    <div style="font-weight:700;"><?= sanitize($order['full_name']) ?></div>
                    <div style="font-size:12px;color:#888;"><?= sanitize($order['email']) ?></div>
                </div>
            </div>
            <a href="customers.php" class="btn-admin-primary btn-admin-sm w-100 justify-content-center"><i class="fas fa-users"></i> View All Customers</a>
        </div>
    </div>
</div>

</div>
<?php include 'includes/footer.php'; ?>