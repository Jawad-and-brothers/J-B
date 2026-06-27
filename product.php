<?php
require_once 'config.php';
$db = getDB();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: shop.php'); exit; }

$stmt = $db->prepare("SELECT p.*, c.name as cat_name, c.id as cat_id FROM products p JOIN categories c ON p.category_id=c.id WHERE p.id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) { header('Location: shop.php'); exit; }

$pageTitle = $p['name'];

// Related products
$related = $db->prepare("SELECT * FROM products WHERE category_id=? AND id!=? LIMIT 4");
$related->bind_param('ii', $p['cat_id'], $id);
$related->execute();
$relatedRes = $related->get_result();
?>
<?php include 'includes/header.php'; ?>

<div class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <li class="breadcrumb-item"><a href="shop.php?cat=<?= $p['cat_id'] ?>"><?= sanitize($p['cat_name']) ?></a></li>
                <li class="breadcrumb-item active"><?= sanitize($p['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Product Image -->
            <div class="col-lg-6">
                <div class="product-detail-img">
                    <img src="images/products/<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>">
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-detail-info">
                    <div class="product-cat mb-2" style="font-size:12px;letter-spacing:1.5px;color:var(--gold);text-transform:uppercase;font-weight:600;">
                        <?= sanitize($p['cat_name']) ?>
                    </div>
                    <h1 style="font-size:2rem;margin-bottom:15px;"><?= sanitize($p['name']) ?></h1>

                    <div class="product-detail-price"><?= formatPrice($p['price']) ?></div>

                    <p style="color:#666;line-height:1.8;margin-bottom:25px;"><?= sanitize($p['description']) ?></p>

                    <div class="d-flex gap-3 align-items-center mb-4 flex-wrap">
                        <span style="font-size:13px;font-weight:600;">Availability:</span>
                        <?php if ($p['stock'] > 0): ?>
                        <span style="color:var(--green-primary);font-weight:600;"><i class="fas fa-check-circle me-1"></i>In Stock (<?= $p['stock'] ?> meters available)</span>
                        <?php else: ?>
                        <span style="color:#dc3545;font-weight:600;"><i class="fas fa-times-circle me-1"></i>Out of Stock</span>
                        <?php endif; ?>
                    </div>

                    <?php if (isLoggedIn() && $p['stock'] > 0): ?>
                    <div class="mb-4">
                        <label style="font-size:13.5px;font-weight:600;display:block;margin-bottom:10px;">Quantity</label>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="qty-selector">
                                <button id="mainQtyDown" type="button">−</button>
                                <input type="number" id="mainQty" name="qty" value="1" min="1" max="<?= $p['stock'] ?>">
                                <button id="mainQtyUp" type="button">+</button>
                            </div>
                            <button class="btn-add-cart btn btn-warning px-4 py-2 fw-bold" data-id="<?= $p['id'] ?>" id="mainAddBtn"
                                style="border-radius:8px;font-size:15px;">
                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                    <?php elseif (!isLoggedIn()): ?>
                    <div class="alert alert-warning d-flex align-items-center" style="font-size:14px;">
                        <i class="fas fa-lock me-2"></i>
                        <span>Please <a href="login.php?msg=login_required" style="color:var(--green-dark);font-weight:700;">login</a> to add items to your cart and place orders.</span>
                    </div>
                    <a href="login.php?msg=login_required" class="btn-green d-inline-block text-center px-5 py-3" style="text-decoration:none;border-radius:8px;font-size:15px;width:auto;">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Purchase
                    </a>
                    <?php else: ?>
                    <div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>This item is currently out of stock.</div>
                    <?php endif; ?>

                    <!-- Product Details -->
                    <div class="mt-4 pt-4" style="border-top:1px solid var(--border);">
                        <div class="row g-3" style="font-size:14px;">
                            <div class="col-6"><i class="fas fa-tag me-2 text-muted"></i><strong>Category:</strong> <?= sanitize($p['cat_name']) ?></div>
                            <div class="col-6"><i class="fas fa-layer-group me-2 text-muted"></i><strong>Stock:</strong> <?= $p['stock'] ?> m</div>
                            <div class="col-6"><i class="fas fa-truck me-2 text-muted"></i><strong>Delivery:</strong> 3-5 days</div>
                            <div class="col-6"><i class="fas fa-shield-alt me-2 text-muted"></i><strong>Quality:</strong> Certified</div>
                        </div>
                    </div>

                    <!-- Social Share -->
                    <div class="mt-4 d-flex gap-2 align-items-center">
                        <span style="font-size:13px;color:#888;">Share:</span>
                        <a href="#" style="color:#3b5998;"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" style="color:#e1306c;"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" style="color:#25d366;"><i class="fab fa-whatsapp fa-lg"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if ($relatedRes->num_rows > 0): ?>
        <div class="mt-6 pt-5" style="border-top:2px solid var(--green-pale);margin-top:60px;">
            <h3 class="mb-4">You May Also Like</h3>
            <div class="row g-4">
                <?php while ($rp = $relatedRes->fetch_assoc()): ?>
                <div class="col-sm-6 col-lg-3">
                    <div class="product-card">
                        <div class="product-img-wrap">
                            <img src="images/products/<?= sanitize($rp['image']) ?>" alt="<?= sanitize($rp['name']) ?>" loading="lazy">
                            <div class="product-actions-overlay">
                                <a href="product.php?id=<?= $rp['id'] ?>" class="btn btn-light btn-sm"><i class="fas fa-eye"></i> View</a>
                                <?php if(isLoggedIn()): ?>
                                <button class="btn btn-warning btn-sm btn-add-cart" data-id="<?= $rp['id'] ?>"><i class="fas fa-cart-plus"></i> Add</button>
                                <?php else: ?>
                                <a href="login.php?msg=login_required" class="btn btn-warning btn-sm"><i class="fas fa-lock"></i> Login</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="product-body">
                            <a href="product.php?id=<?= $rp['id'] ?>" class="product-name d-block"><?= sanitize($rp['name']) ?></a>
                            <div class="product-price"><?= formatPrice($rp['price']) ?></div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
// For product page add to cart — attach mainQty value
document.addEventListener('DOMContentLoaded', function() {
    const mainBtn = document.getElementById('mainAddBtn');
    const mainQtyInput = document.getElementById('mainQty');
    if (mainBtn && mainQtyInput) {
        mainBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            const qty = mainQtyInput.value || 1;
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';
            fetch('php/cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=add&product_id=${productId}&quantity=${qty}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.innerHTML = '<i class="fas fa-check me-1"></i> Added to Cart!';
                    this.classList.add('btn-success');
                    document.querySelectorAll('.cart-badge').forEach(el => { el.textContent = data.cart_count; el.style.display='flex'; });
                    setTimeout(() => { this.innerHTML = originalText; this.disabled = false; this.classList.remove('btn-success'); }, 2500);
                } else {
                    showToast(data.message || 'Error', 'danger');
                    if (data.redirect) window.location.href = data.redirect;
                    this.innerHTML = originalText; this.disabled = false;
                }
            });
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>