<?php
$adminTitle = 'Dashboard';
require_once 'admin_config.php';
requireAdmin();
$db = getDB();
$stats = getDashboardStats($db);

// Recent orders
$recentOrders = $db->query("SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC LIMIT 8");

// Sales chart data (last 7 days)
$salesData = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('D', strtotime($date));
    $r = $db->query("SELECT COALESCE(SUM(total_amount),0) as s FROM orders WHERE DATE(created_at)='$date' AND status!='cancelled'");
    $salesData[] = ['label' => $label, 'value' => (float)$r->fetch_assoc()['s']];
}

// Top products
$topProducts = $db->query("SELECT p.name, p.image, SUM(oi.quantity) as sold FROM order_items oi JOIN products p ON oi.product_id=p.id GROUP BY oi.product_id ORDER BY sold DESC LIMIT 5");
?>
<?php include 'includes/sidebar.php'; ?>
<div class="page-body">

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-tshirt"></i></div>
                <div><div class="stat-value"><?= $stats['total_products'] ?></div><div class="stat-label">Products</div></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon gold"><i class="fas fa-shopping-bag"></i></div>
                <div><div class="stat-value"><?= $stats['total_orders'] ?></div><div class="stat-label">Orders</div></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                <div><div class="stat-value"><?= $stats['total_users'] ?></div><div class="stat-label">Customers</div></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon teal"><i class="fas fa-rupee-sign"></i></div>
                <div><div class="stat-value" style="font-size:1.2rem;">PKR <?= number_format($stats['total_revenue'],0) ?></div><div class="stat-label">Revenue</div></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                <div><div class="stat-value"><?= $stats['pending_orders'] ?></div><div class="stat-label">Pending</div></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-tags"></i></div>
                <div><div class="stat-value"><?= $stats['categories'] ?></div><div class="stat-label">Categories</div></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Sales Chart -->
        <div class="col-lg-8">
            <div class="data-card">
                <div class="data-card-header">
                    <div class="data-card-title"><i class="fas fa-chart-line me-2 text-success"></i>Sales — Last 7 Days</div>
                </div>
                <div style="padding:20px;">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <!-- Top Products -->
        <div class="col-lg-4">
            <div class="data-card h-100">
                <div class="data-card-header">
                    <div class="data-card-title"><i class="fas fa-fire me-2 text-warning"></i>Top Selling</div>
                </div>
                <div style="padding:10px 0;">
                    <?php if ($topProducts->num_rows == 0): ?>
                    <p class="text-center text-muted py-3" style="font-size:13px;">No sales data yet.</p>
                    <?php else: $rank=1; while ($tp = $topProducts->fetch_assoc()): ?>
                    <div style="display:flex;align-items:center;gap:12px;padding:10px 20px;border-bottom:1px solid #f5f5f5;">
                        <div style="width:24px;height:24px;background:<?= $rank==1?'var(--gold)':($rank==2?'#aaa':($rank==3?'#cd7f32':'#e8f0eb')) ?>;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:<?= $rank<=3?'#fff':'var(--green-primary)' ?>;"><?= $rank ?></div>
                        <img src="../images/products/<?= sanitize($tp['image']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:6px;" alt="">
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= sanitize($tp['name']) ?></div>
                            <div style="font-size:11px;color:#888;"><?= $tp['sold'] ?> sold</div>
                        </div>
                    </div>
                    <?php $rank++; endwhile; endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="data-card">
        <div class="data-card-header">
            <div class="data-card-title"><i class="fas fa-list me-2 text-success"></i>Recent Orders</div>
            <a href="orders.php" class="btn-admin-primary btn-admin-sm"><i class="fas fa-eye"></i> View All</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr>
                    <th>Order #</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Action</th>
                </tr></thead>
                <tbody>
                <?php if ($recentOrders->num_rows == 0): ?>
                <tr><td colspan="7" class="text-center py-4 text-muted">No orders yet.</td></tr>
                <?php else: while ($o = $recentOrders->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= sanitize($o['order_number']) ?></strong></td>
                    <td><?= sanitize($o['full_name']) ?></td>
                    <td><strong>PKR <?= number_format($o['total_amount'],0) ?></strong></td>
                    <td><?= ucwords(str_replace('_',' ',$o['payment_method'])) ?></td>
                    <td><span class="badge-status badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                    <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                    <td><a href="order_detail.php?id=<?= $o['id'] ?>" class="btn-admin-primary btn-admin-sm"><i class="fas fa-eye"></i></a></td>
                </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div><!-- end page-body -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const salesData = <?= json_encode($salesData) ?>;
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: salesData.map(d => d.label),
        datasets: [{
            label: 'Sales (PKR)',
            data: salesData.map(d => d.value),
            backgroundColor: 'rgba(74,124,89,0.18)',
            borderColor: '#4a7c59',
            borderWidth: 2,
            borderRadius: 6,
            fill: true
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'PKR ' + v.toLocaleString(), font: { size: 11 } }, grid: { color: '#f0f0f0' } },
            x: { grid: { display: false }, ticks: { font: { size: 12 } } }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>