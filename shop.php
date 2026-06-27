<?php
$pageTitle = 'Shop';
require_once 'config.php';
$db = getDB();

// Filters
$cat_id = (int)($_GET['cat'] ?? 0);
$sort = sanitize($_GET['sort'] ?? 'newest');
$max_price = (int)($_GET['max_price'] ?? 20000);
$search = sanitize($_GET['q'] ?? '');

// Build query
$where = ['1=1'];
$params = [];
$types = '';

if ($cat_id > 0) { $where[] = 'p.category_id = ?'; $params[] = $cat_id; $types .= 'i'; }
if ($max_price > 0) { $where[] = 'p.price <= ?'; $params[] = $max_price; $types .= 'i'; }
if ($search) { $where[] = '(p.name LIKE ? OR p.description LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; $types .= 'ss'; }

$order = match($sort) {
    'price_low' => 'p.price ASC',
    'price_high' => 'p.price DESC',
    'name' => 'p.name ASC',
    default => 'p.id DESC'
};

$sql = "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE " . implode(' AND ', $where) . " ORDER BY $order";
$stmt = $db->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();
$total = $products->num_rows;

// Categories for sidebar
$categories = $db->query("SELECT c.*, COUNT(p.id) as cnt FROM categories c LEFT JOIN products p ON c.id=p.category_id GROUP BY c.id");

// Active category name
$activeCatName = 'All Products';
if ($cat_id) {
    $r = $db->query("SELECT name FROM categories WHERE id=$cat_id");
    if ($r) $activeCatName = $r->fetch_assoc()['name'] ?? 'All Products';
}
?>
<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2><?= sanitize($activeCatName) ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <?php if ($cat_id): ?>
                <li class="breadcrumb-item active"><?= sanitize($activeCatName) ?></li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="shop-sidebar">
                    <!-- Search -->
                    <div class="mb-4">
                        <div class="sidebar-title">Search Products</div>
                        <form method="GET" action="shop.php">
                            <div class="input-group">
                                <input type="text" class="form-control" name="q" value="<?= sanitize($search) ?>" placeholder="Search fabrics...">
                                <button class="btn btn-sm" style="background:var(--green-primary);color:#fff;" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                            <?php if ($cat_id): ?><input type="hidden" name="cat" value="<?= $cat_id ?>"> <?php endif; ?>
                        </form>
                    </div>

                    <!-- Categories -->
                    <div class="mb-4">
                        <div class="sidebar-title">Categories</div>
                        <a href="shop.php" class="cat-filter-item d-flex <?= !$cat_id?'active':'' ?>">
                            All Products <span class="badge"><?= $db->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'] ?></span>
                        </a>
                        <?php $categories->data_seek(0); while ($cat = $categories->fetch_assoc()): ?>
                        <a href="shop.php?cat=<?= $cat['id'] ?>" class="cat-filter-item d-flex <?= $cat_id==$cat['id']?'active':'' ?>">
                            <?= sanitize($cat['name']) ?> <span class="badge"><?= $cat['cnt'] ?></span>
                        </a>
                        <?php endwhile; ?>
                    </div>

                    <!-- Price Filter -->
                    <div class="mb-4">
                        <div class="sidebar-title">Price Range</div>
                        <form method="GET" action="shop.php" id="priceForm">
                            <?php if ($cat_id): ?><input type="hidden" name="cat" value="<?= $cat_id ?>"><?php endif; ?>
                            <?php if ($search): ?><input type="hidden" name="q" value="<?= sanitize($search) ?>"><?php endif; ?>
                            <input type="hidden" name="sort" value="<?= sanitize($sort) ?>">
                            <div class="price-range">
                                <input type="range" id="priceRange" name="max_price" min="500" max="20000" step="500" value="<?= $max_price ?>">
                            </div>
                            <div class="d-flex justify-content-between mt-2 mb-3" style="font-size:13px;">
                                <span>PKR 500</span>
                                <span id="priceVal">PKR <?= number_format($max_price) ?></span>
                            </div>
                            <button type="submit" class="btn btn-sm w-100" style="background:var(--green-pale);color:var(--green-primary);border:1px solid var(--green-primary);font-weight:600;">Apply Filter</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <p class="mb-0" style="font-size:14px;color:#666;">
                        Showing <strong><?= $total ?></strong> product<?= $total != 1 ? 's' : '' ?>
                        <?= $search ? 'for "<strong>' . sanitize($search) . '</strong>"' : '' ?>
                    </p>
                    <div class="d-flex gap-2 align-items-center">
                        <label style="font-size:13px;color:#666;white-space:nowrap;">Sort by:</label>
                        <select class="form-select form-select-sm" style="width:160px;" onchange="location.href=this.value">
                            <?php
                            $sorts = ['newest'=>'Newest First','price_low'=>'Price: Low to High','price_high'=>'Price: High to Low','name'=>'Name A-Z'];
                            foreach ($sorts as $val => $label):
                                $url = http_build_query(array_merge($_GET, ['sort'=>$val]));
                            ?>
                            <option value="shop.php?<?= $url ?>" <?= $sort==$val?'selected':'' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?php if ($total === 0): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search" style="font-size:60px;color:#ccc;"></i>
                    <h4 class="mt-3 text-muted">No products found</h4>
                    <p class="text-muted">Try adjusting your filters or search terms.</p>
                    <a href="shop.php" class="btn btn-outline-success">View All Products</a>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php while ($p = $products->fetch_assoc()): ?>
                    <div class="col-sm-6 col-xl-4">
                        <div class="product-card">
                            <div class="product-img-wrap">
                                <img src="images/products/<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy">
                                <?php if ($p['featured']): ?><span class="product-badge">Featured</span><?php endif; ?>
                                <div class="product-actions-overlay">
                                    <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-light btn-sm"><i class="fas fa-eye"></i> View</a>
                                    <?php if(isLoggedIn()): ?>
                                    <button class="btn btn-warning btn-sm btn-add-cart" data-id="<?= $p['id'] ?>"><i class="fas fa-cart-plus"></i> Cart</button>
                                    <?php else: ?>
                                    <a href="login.php?msg=login_required" class="btn btn-warning btn-sm"><i class="fas fa-lock"></i> Login</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="product-body">
                                <div class="product-cat"><?= sanitize($p['cat_name']) ?></div>
                                <a href="product.php?id=<?= $p['id'] ?>" class="product-name d-block"><?= sanitize($p['name']) ?></a>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div class="product-price"><?= formatPrice($p['price']) ?></div>
                                    <span style="font-size:11px;color:<?= $p['stock']>0?'var(--green-primary)':'#dc3545' ?>;">
                                        <?= $p['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>