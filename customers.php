<?php
$adminTitle = 'Customers';
require_once 'admin_config.php';
requireAdmin();
$db = getDB();

$search = sanitize($_GET['q'] ?? '');
$where = ['1=1'];
if ($search) $where[] = "(u.full_name LIKE '%".addslashes($search)."%' OR u.email LIKE '%".addslashes($search)."%' OR u.phone LIKE '%".addslashes($search)."%')";

$customers = $db->query("SELECT u.*, COUNT(o.id) as order_count, COALESCE(SUM(o.total_amount),0) as total_spent FROM users u LEFT JOIN orders o ON u.id=o.user_id AND o.status!='cancelled' WHERE ".implode(' AND ',$where)." GROUP BY u.id ORDER BY u.created_at DESC");
?>
<?php include 'includes/sidebar.php'; ?>
<div class="page-body">

<div class="data-card">
    <div class="data-card-header">
        <div class="data-card-title"><i class="fas fa-users me-2 text-success"></i>Customers (<?= $customers->num_rows ?>)</div>
    </div>
    <div style="padding:14px 20px;background:#fafafa;border-bottom:1px solid var(--border);">
        <form method="GET" class="d-flex gap-2">
            <div class="search-bar" style="flex:1;max-width:360px;">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" name="q" value="<?= sanitize($search) ?>" placeholder="Search by name, email or phone...">
            </div>
            <button type="submit" class="btn-admin-primary"><i class="fas fa-search"></i> Search</button>
            <?php if ($search): ?><a href="customers.php" class="btn-admin-primary" style="background:#6c757d;"><i class="fas fa-times"></i></a><?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>City</th><th>Orders</th><th>Total Spent</th><th>Joined</th><th>Status</th></tr></thead>
            <tbody>
            <?php if ($customers->num_rows == 0): ?>
            <tr><td colspan="9" class="text-center py-5 text-muted"><i class="fas fa-users fa-2x mb-2 d-block" style="color:#ccc;"></i>No customers found.</td></tr>
            <?php else: $i=1; while ($c = $customers->fetch_assoc()): ?>
            <tr>
                <td style="color:#aaa;font-size:12px;"><?= $i++ ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;background:var(--green-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--green-primary);font-weight:700;font-size:13px;flex-shrink:0;"><?= strtoupper($c['full_name'][0]) ?></div>
                        <strong><?= sanitize($c['full_name']) ?></strong>
                    </div>
                </td>
                <td style="font-size:13px;"><?= sanitize($c['email']) ?></td>
                <td><?= sanitize($c['phone']) ?></td>
                <td><?= sanitize($c['city'] ?: '—') ?></td>
                <td><span style="background:var(--green-pale);color:var(--green-primary);font-weight:700;padding:3px 10px;border-radius:50px;font-size:12px;"><?= $c['order_count'] ?></span></td>
                <td><strong>PKR <?= number_format($c['total_spent'],0) ?></strong></td>
                <td style="font-size:12.5px;"><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                <td>
                    <span style="background:<?= $c['is_active']?'#d4edda':'#f8d7da' ?>;color:<?= $c['is_active']?'#155724':'#721c24' ?>;font-size:11px;font-weight:600;padding:3px 10px;border-radius:50px;">
                        <?= $c['is_active']?'Active':'Inactive' ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
<?php include 'includes/footer.php'; ?>