<?php
$adminTitle = 'Products';
require_once 'admin_config.php';
requireAdmin();
$db = getDB();

// Handle delete
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    // Get image to delete
    $img = $db->query("SELECT image FROM products WHERE id=$did")->fetch_assoc();
    $db->query("DELETE FROM products WHERE id=$did");
    $_SESSION['admin_msg'] = ['type'=>'success','text'=>'Product deleted successfully.'];
    header('Location: products.php'); exit;
}

// Handle featured toggle
if (isset($_GET['toggle_featured'])) {
    $tid = (int)$_GET['toggle_featured'];
    $db->query("UPDATE products SET featured = 1-featured WHERE id=$tid");
    header('Location: products.php'); exit;
}

// Filters
$cat_filter = (int)($_GET['cat'] ?? 0);
$search = sanitize($_GET['q'] ?? '');
$where = ['1=1'];
if ($cat_filter) $where[] = "p.category_id=$cat_filter";
if ($search) $where[] = "p.name LIKE '%".addslashes($search)."%'";

$products = $db->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE ".implode(' AND ',$where)." ORDER BY p.id DESC");
$categories = $db->query("SELECT * FROM categories ORDER BY name");

$msg = $_SESSION['admin_msg'] ?? null; unset($_SESSION['admin_msg']);
?>
<?php include 'includes/sidebar.php'; ?>
<div class="page-body">

<?php if ($msg): ?>
<div class="alert-admin <?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'exclamation-circle' ?> me-2"></i><?= sanitize($msg['text']) ?></div>
<?php endif; ?>

<div class="data-card">
    <div class="data-card-header">
        <div class="data-card-title"><i class="fas fa-tshirt me-2 text-success"></i>All Products (<?= $products->num_rows ?>)</div>
        <a href="add_product.php" class="btn-admin-primary"><i class="fas fa-plus"></i> Add New Product</a>
    </div>

    <!-- Filters -->
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);background:#fafafa;">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <div class="search-bar" style="flex:1;min-width:180px;">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" name="q" value="<?= sanitize($search) ?>" placeholder="Search products...">
            </div>
            <select class="form-select" name="cat" style="width:180px;">
                <option value="">All Categories</option>
                <?php $categories->data_seek(0); while($c=$categories->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>" <?= $cat_filter==$c['id']?'selected':'' ?>><?= sanitize($c['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn-admin-primary"><i class="fas fa-filter"></i> Filter</button>
            <a href="products.php" class="btn-admin-primary" style="background:#6c757d;"><i class="fas fa-times"></i> Clear</a>
        </form>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead><tr>
                <th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Featured</th><th style="width:160px;">Actions</th>
            </tr></thead>
            <tbody>
            <?php if ($products->num_rows == 0): ?>
            <tr><td colspan="7" class="text-center py-5 text-muted"><i class="fas fa-box-open fa-2x mb-2 d-block" style="color:#ccc;"></i>No products found.</td></tr>
            <?php else: while ($p = $products->fetch_assoc()): ?>
            <tr>
                <td><img src="../images/products/<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>"></td>
                <td>
                    <strong><?= sanitize($p['name']) ?></strong>
                    <div style="font-size:11.5px;color:#888;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= sanitize(substr($p['description'],0,60)) ?>...</div>
                </td>
                <td><?= sanitize($p['cat_name']) ?></td>
                <td><strong style="color:var(--green-primary);">PKR <?= number_format($p['price'],0) ?></strong></td>
                <td>
                    <span style="color:<?= $p['stock']>5?'var(--green-primary)':($p['stock']>0?'#fd7e14':'#dc3545') ?>;font-weight:600;">
                        <?= $p['stock'] ?> m
                    </span>
                </td>
                <td>
                    <label class="toggle-switch" title="Toggle Featured">
                        <input type="checkbox" <?= $p['featured']?'checked':'' ?> onchange="location.href='products.php?toggle_featured=<?= $p['id'] ?>'">
                        <span class="toggle-slider"></span>
                    </label>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="../product.php?id=<?= $p['id'] ?>" class="btn-admin-primary btn-admin-sm" target="_blank" title="View"><i class="fas fa-eye"></i></a>
                        <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn-admin-gold btn-admin-sm" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="products.php?delete=<?= $p['id'] ?>" class="btn-admin-danger btn-admin-sm btn-confirm-delete" title="Delete"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
<?php include 'includes/footer.php'; ?>