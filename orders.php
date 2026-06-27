<?php
$adminTitle = 'Orders';
require_once 'admin_config.php';
requireAdmin();
$db = getDB();

// Filters
$status_filter = sanitize($_GET['status'] ?? '');
$search = sanitize($_GET['q'] ?? '');

$where = ['1=1'];
if ($status_filter) $where[] = "o.status='".addslashes($status_filter)."'";
if ($search) $where[] = "(o.order_number LIKE '%".addslashes($search)."%' OR u.full_name LIKE '%".addslashes($search)."%' OR o.shipping_phone LIKE '%".addslashes($search)."%')";

$orders = $db->query("SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id=u.id WHERE ".implode(' AND ',$where)." ORDER BY o.created_at DESC");

$statusColors = ['pending'=>'badge-pending','processing'=>'badge-processing','shipped'=>'badge-shipped','delivered'=>'badge-delivered','cancelled'=>'badge-cancelled'];
$msg = $_SESSION['admin_msg'] ?? null; unset($_SESSION['admin_msg']);
?>
<?php include 'includes/sidebar.php'; ?>
<div class="page-body">

<?php if ($msg): ?>
<div class="alert-admin <?= $msg['type'] ?>"><i class="fas fa-check-circle me-2"></i><?= sanitize($msg['text']) ?></div>
<?php endif; ?>

<div class="data-card">
    <div class="data-card-header">
        <div class="data-card-title"><i class="fas fa-shopping-bag me-2 text-success"></i>Orders (<?= $orders->num_rows ?>)</div>
    </div>

    <!-- Filters Bar -->
    <div style="padding:14px 20px;background:#fafafa;border-bottom:1px solid var(--border);">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <div class="search-bar" style="flex:1;min-width:180px;">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" name="q" value="<?= sanitize($search) ?>" placeholder="Order #, name, phone...">
            </div>
            <select class="form-select" name="status" style="width:160px;">
                <option value="">All Status</option>
                <?php foreach(['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= $status_filter===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-admin-primary"><i class="fas fa-filter"></i> Filter</button>
            <a href="orders.php" class="btn-admin-primary" style="background:#6c757d;"><i class="fas fa-times"></i></a>
        </form>
    </div>

    <!-- Status Pills -->
    <div style="padding:12px 20px;display:flex;gap:8px;flex-wrap:wrap;border-bottom:1px solid #f0f0f0;">
        <?php
        $statuses = ['all'=>'All','pending'=>'Pending','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'];
        foreach ($statuses as $sv => $sl):
            $count = ($sv === 'all') ? $db->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c']
                     : $db->query("SELECT COUNT(*) as c FROM orders WHERE status='$sv'")->fetch_assoc()['c'];
            $active = ($status_filter === $sv) || ($sv === 'all' && !$status_filter);
        ?>
        <a href="orders.php?status=<?= $sv==='all'?'':$sv ?>" style="font-size:12px;font-weight:600;padding:5px 14px;border-radius:50px;background:<?= $active?'var(--green-primary)':'#f0f0f0' ?>;color:<?= $active?'#fff':'#666' ?>;">
            <?= $sl ?> <span style="opacity:0.8;">(<?= $count ?>)</span>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead><tr>
                <th>Order #</th><th>Customer</th><th>City</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Action</th>
            </tr></thead>
            <tbody>
            <?php if ($orders->num_rows == 0): ?>
            <tr><td colspan="8" class="text-center py-5 text-muted"><i class="fas fa-box-open fa-2x mb-2 d-block" style="color:#ccc;"></i>No orders found.</td></tr>
            <?php else: while ($o = $orders->fetch_assoc()): ?>
            <tr>
                <td><strong style="color:var(--green-primary);"><?= sanitize($o['order_number']) ?></strong></td>
                <td>
                    <div style="font-weight:600;"><?= sanitize($o['full_name']) ?></div>
                    <div style="font-size:11.5px;color:#888;"><?= sanitize($o['shipping_phone']) ?></div>
                </td>
                <td><?= sanitize($o['shipping_city']) ?></td>
                <td><strong>PKR <?= number_format($o['total_amount'],0) ?></strong></td>
                <td style="font-size:12.5px;"><?= ucwords(str_replace('_',' ',$o['payment_method'])) ?></td>
                <td><span class="badge-status <?= $statusColors[$o['status']] ?? '' ?>"><?= ucfirst($o['status']) ?></span></td>
                <td style="font-size:12.5px;"><?= date('d M Y', strtotime($o['created_at'])) ?><br><span style="color:#aaa;font-size:11px;"><?= date('h:i A', strtotime($o['created_at'])) ?></span></td>
                <td><a href="order_detail.php?id=<?= $o['id'] ?>" class="btn-admin-primary btn-admin-sm"><i class="fas fa-eye"></i> View</a></td>
            </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
<?php include 'includes/footer.php'; ?>