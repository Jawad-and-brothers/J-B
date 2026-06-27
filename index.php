<?php
$pageTitle = 'Home';
require_once 'config.php';
$db = getDB();

// Fetch featured products
$featured = $db->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.featured=1 ORDER BY p.id DESC LIMIT 8");

// Fetch categories with product counts
$cats = $db->query("SELECT c.*, COUNT(p.id) as prod_count FROM categories c LEFT JOIN products p ON c.id=p.category_id GROUP BY c.id ORDER BY c.name");

// Category image map (uses actual product images)
$catImages = [1=>'p.jpeg', 2=>'p.jpeg', 3=>'shawl.jpeg', 4=>'p102.png', 5=>'p102.png'];
?>
<?php include 'includes/header.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 hero-content">
                <p class="hero-subtitle">Premium Fabrics Since Generations</p>
                <h1>Discover the Art of<br><span>Elegant Fabrics</span><br>& Fine Craftsmanship</h1>
                <p>Pakistan's finest manufacturer, wholesaler, and retailer of premium Banarsi, silk, lawn, and embroidered fabrics and suits.</p>
                <div>
                    <a href="shop.php" class="btn-hero-primary"><i class="fas fa-shopping-bag me-2"></i>Shop Collection</a>
                    <a href="about.php" class="btn-hero-secondary">Learn More</a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat"><div class="num">500+</div><div class="lbl">Products</div></div>
                    <div class="hero-stat"><div class="num">10K+</div><div class="lbl">Customers</div></div>
                    <div class="hero-stat"><div class="num">15+</div><div class="lbl">Years</div></div>
                    <div class="hero-stat"><div class="num">4</div><div class="lbl">Cities</div></div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center mt-4 mt-lg-0">
                <div style="border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.4);max-height:450px;">
                    <img src="./images/products/p101.jfif" alt="Featured Fabric" style="width:100%;height:450px;object-fit:cover;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES BAR -->
<div style="background:#fff;border-bottom:1px solid #eee;">
    <div class="container">
        <div class="row text-center g-0">
            <div class="col-6 col-md-3" style="padding:20px 15px;border-right:1px solid #eee;">
                <i class="fas fa-truck" style="color:var(--green-primary);font-size:22px;"></i>
                <div style="font-size:13px;font-weight:600;margin-top:8px;">Free Delivery</div>
                <div style="font-size:11px;color:#888;">Orders over PKR 5,000</div>
            </div>
            <div class="col-6 col-md-3" style="padding:20px 15px;border-right:1px solid #eee;">
                <i class="fas fa-award" style="color:var(--green-primary);font-size:22px;"></i>
                <div style="font-size:13px;font-weight:600;margin-top:8px;">Premium Quality</div>
                <div style="font-size:11px;color:#888;">Guaranteed authentic fabrics</div>
            </div>
            <div class="col-6 col-md-3" style="padding:20px 15px;border-right:1px solid #eee;">
                <i class="fas fa-undo-alt" style="color:var(--green-primary);font-size:22px;"></i>
                <div style="font-size:13px;font-weight:600;margin-top:8px;">Easy Returns</div>
                <div style="font-size:11px;color:#888;">7-day hassle-free returns</div>
            </div>
            <div class="col-6 col-md-3" style="padding:20px 15px;">
                <i class="fas fa-headset" style="color:var(--green-primary);font-size:22px;"></i>
                <div style="font-size:13px;font-weight:600;margin-top:8px;">24/7 Support</div>
                <div style="font-size:11px;color:#888;">WhatsApp & phone support</div>
            </div>
        </div>
    </div>
</div>

<!-- CATEGORIES -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <p style="color:var(--gold);font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:600;">Browse By Type</p>
            <h2 class="section-title">Shop by Category</h2>
            <div class="section-divider mx-auto"></div>
        </div>
        <div class="row g-3">
            <?php
            $cats->data_seek(0);
            $catArr = [];
            while($cat = $cats->fetch_assoc()) $catArr[] = $cat;
            $bigCats = array_slice($catArr, 0, 2);
            $smallCats = array_slice($catArr, 2, 3);
            ?>
            <div class="col-md-8">
                <div class="row g-3 h-100">
                    <?php foreach($bigCats as $cat): $imgFile = $catImages[$cat['id']] ?? 'p1.jpeg'; ?>
                    <div class="col-6">
                        <a href="shop.php?cat=<?= $cat['id'] ?>" class="cat-card d-block" style="height:260px;">
                            <img src="images/products/<?= $imgFile ?>" alt="<?= sanitize($cat['name']) ?>">
                            <div class="cat-overlay">
                                <div>
                                    <div class="cat-label"><?= sanitize($cat['name']) ?></div>
                                    <div class="cat-count"><?= $cat['name'] ?></div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row g-3">
                    <?php foreach($smallCats as $cat): $imgFile = $catImages[$cat['id']] ?? 'p1.jpeg'; ?>
                    <div class="col-12">
                        <a href="shop.php?cat=<?= $cat['id'] ?>" class="cat-card d-block" style="height:150px;">
                            <img src="images/products/<?= $imgFile ?>" alt="<?= sanitize($cat['name']) ?>">
                            <div class="cat-overlay">
                                <div>
                                    <div class="cat-label"><?= sanitize($cat['name']) ?></div>
                                    <div class="cat-count"><?= $cat['name'] ?></div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="py-5 bg-green-pale">
    <div class="container">
        <div class="text-center mb-4">
            <p style="color:var(--gold);font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:600;">Hand-picked for You</p>
            <h2 class="section-title">Featured Collection</h2>
            <div class="section-divider mx-auto"></div>
            <p class="section-sub">Exquisite fabrics curated from our finest selection</p>
        </div>
        <div class="row g-4">
            <?php $featured->data_seek(0); while($p = $featured->fetch_assoc()): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="product-card">
                    <div class="product-img-wrap">
                        <img src="images/products/<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy">
                        <span class="product-badge">Featured</span>
                        <div class="product-actions-overlay">
                            <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-light btn-sm"><i class="fas fa-eye"></i> View</a>
                            <?php if(isLoggedIn()): ?>
                            <button class="btn btn-warning btn-sm btn-add-cart" data-id="<?= $p['id'] ?>"><i class="fas fa-cart-plus"></i> Add</button>
                            <?php else: ?>
                            <a href="login.php?msg=login_required" class="btn btn-warning btn-sm"><i class="fas fa-lock"></i> Login</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product-body">
                        <div class="product-cat"><?= sanitize($p['cat_name']) ?></div>
                        <a href="product.php?id=<?= $p['id'] ?>" class="product-name"><?= sanitize($p['name']) ?></a>
                        <div class="product-price"><?= formatPrice($p['price']) ?></div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-4">
            <a href="shop.php" class="btn btn-outline-success px-5 py-2" style="border-color:var(--green-primary);color:var(--green-primary);border-radius:50px;font-weight:600;">View All Products <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

<!-- ABOUT STRIP -->
<section class="about-strip">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div style="border-radius:16px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,0.4);">
                    <img src="images/products/p102.png" alt="Our Store" style="width:100%;height:380px;object-fit:cover;">
                </div>
            </div>
            <div class="col-lg-7">
                <p style="color:var(--gold);font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:600;">Who We Are</p>
                <h2 class="mb-3">Jawad &amp; Brothers — Crafting Excellence in Every Thread</h2>
                <p style="color:rgba(255,255,255,0.8);margin-bottom:30px;">For over 15 years, J&B has been Karachi's most trusted name in premium fabrics. From Banarsi silk to hand-embroidered suits, we bring the finest textiles directly from manufacturers to your doorstep.</p>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-industry"></i></div>
                    <div><strong style="color:#fff;">Direct Manufacturer</strong><br><span style="color:rgba(255,255,255,0.7);font-size:14px;">We manufacture in-house ensuring unmatched quality control at every step.</span></div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-boxes"></i></div>
                    <div><strong style="color:#fff;">Wholesale &amp; Retail</strong><br><span style="color:rgba(255,255,255,0.7);font-size:14px;">Best prices for bulk orders. Individual customers also welcome at retail rates.</span></div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <div><strong style="color:#fff;">100% Authentic</strong><br><span style="color:rgba(255,255,255,0.7);font-size:14px;">Every fabric is certified authentic — no imitations, only the real thing.</span></div>
                </div>
                <a href="about.php" class="btn-hero-primary mt-2">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- NEW ARRIVALS -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <p style="color:var(--gold);font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:600;">Just In</p>
            <h2 class="section-title">New Arrivals</h2>
            <div class="section-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            <?php
            $new = $db->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC LIMIT 4");
            while($p = $new->fetch_assoc()):
            ?>
            <div class="col-sm-6 col-lg-3">
                <div class="product-card">
                    <div class="product-img-wrap">
                        <img src="images/products/<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy">
                        <span class="product-badge new">New</span>
                        <div class="product-actions-overlay">
                            <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-light btn-sm"><i class="fas fa-eye"></i> View</a>
                            <?php if(isLoggedIn()): ?>
                            <button class="btn btn-warning btn-sm btn-add-cart" data-id="<?= $p['id'] ?>"><i class="fas fa-cart-plus"></i> Add</button>
                            <?php else: ?>
                            <a href="login.php?msg=login_required" class="btn btn-warning btn-sm"><i class="fas fa-lock"></i> Login</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product-body">
                        <div class="product-cat"><?= sanitize($p['cat_name']) ?></div>
                        <a href="product.php?id=<?= $p['id'] ?>" class="product-name"><?= sanitize($p['name']) ?></a>
                        <div class="product-price"><?= formatPrice($p['price']) ?></div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="py-5 bg-green-pale">
    <div class="container">
        <div class="text-center mb-4">
            <p style="color:var(--gold);font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:600;">Customer Reviews</p>
            <h2 class="section-title">What Our Customers Say</h2>
            <div class="section-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            <?php
            $reviews = [
                ['name'=>'Sana Malik','city'=>'Karachi','text'=>'Absolutely stunning Banarsi fabric! The quality exceeded my expectations. My bridal suit turned out perfect. Will definitely order again.','rating'=>5],
                ['name'=>'Ayesha Fatima','city'=>'Lahore','text'=>'Best lawn suits in Karachi! The embroidered dupatta is so detailed and the stitching quality is amazing. Fast delivery too.','rating'=>5],
                ['name'=>'Nadia Khan','city'=>'Islamabad','text'=>'Ordered the crimson embroidered suit for my sister\'s wedding. Everyone was asking where I got it from. Highly recommended!','rating'=>5],
            ];
            foreach($reviews as $r):
            ?>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="star-rating mb-2"><?= str_repeat('★', $r['rating']) ?></div>
                    <p style="font-size:14px;color:#555;line-height:1.7;"><?= $r['text'] ?></p>
                    <div class="testimonial-author">
                        <div class="author-avatar"><?= strtoupper($r['name'][0]) ?></div>
                        <div>
                            <div style="font-weight:700;font-size:14px;"><?= $r['name'] ?></div>
                            <div style="font-size:12px;color:#888;"><?= $r['city'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA BANNER -->
<section style="background:linear-gradient(135deg,var(--green-dark),#1d3d20);padding:60px 0;color:#fff;text-align:center;">
    <div class="container">
        <h2 style="color:#fff;font-size:2rem;margin-bottom:12px;">Ready to Find Your Perfect Fabric?</h2>
        <p style="color:rgba(255,255,255,0.8);margin-bottom:28px;font-size:16px;">Browse our complete collection of premium Pakistani fabrics and suits.</p>
        <a href="shop.php" class="btn-hero-primary me-3"><i class="fas fa-shopping-bag me-2"></i>Shop Now</a>
        <a href="contact.php" class="btn-hero-secondary">Contact Us</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>