<?php
$pageTitle = 'About Us';
require_once 'config.php';
?>
<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2>About Us</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">About Us</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Hero Strip -->
<section style="background:linear-gradient(135deg,var(--green-dark) 0%,#2d5a2d 100%);padding:70px 0;color:#fff;overflow:hidden;position:relative;">
    <div style="position:absolute;inset:0;background:url('images/products/p102.jpeg') center/cover no-repeat;opacity:0.10;"></div>
    <div class="container" style="position:relative;z-index:1;">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <p style="color:var(--gold);font-size:12px;letter-spacing:3px;text-transform:uppercase;font-weight:600;margin-bottom:12px;">Our Story</p>
                <h1 style="font-size:clamp(2rem,4vw,3rem);color:#fff;margin-bottom:20px;line-height:1.2;">Crafting Excellence<br>in Every Thread</h1>
                <p style="color:rgba(255,255,255,0.82);font-size:16px;line-height:1.9;margin-bottom:24px;">
                    Jawad & Brothers has been Karachi's most trusted name in premium fabrics for over 15 years. Founded on the principles of quality, authenticity, and customer satisfaction, we bring the finest Pakistani textiles from the loom to your hands.
                </p>
                <p style="color:rgba(255,255,255,0.75);font-size:15px;line-height:1.8;">
                    From handwoven Banarsi silk to delicate lawn suits and intricate embroidered bridal wear, every piece in our collection reflects the rich heritage of Pakistani craftsmanship.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div style="border-radius:14px;overflow:hidden;height:200px;">
                            <img src="images/products/p101.jpeg" style="width:100%;height:100%;object-fit:cover;" alt="">
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="border-radius:14px;overflow:hidden;height:200px;">
                            <img src="images/products/p1.jpeg" style="width:100%;height:100%;object-fit:cover;" alt="">
                        </div>
                    </div>
                    <div class="col-12">
                        <div style="border-radius:14px;overflow:hidden;height:160px;">
                            <img src="images/products/p5.jpeg" style="width:100%;height:100%;object-fit:cover;" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section style="background:#fff;padding:50px 0;border-bottom:1px solid #eee;">
    <div class="container">
        <div class="row g-0 text-center">
            <?php
            $stats = [
                ['num'=>'15+','label'=>'Years of Excellence','icon'=>'fas fa-medal'],
                ['num'=>'500+','label'=>'Fabric Varieties','icon'=>'fas fa-layer-group'],
                ['num'=>'10,000+','label'=>'Happy Customers','icon'=>'fas fa-smile'],
                ['num'=>'4','label'=>'Cities Served','icon'=>'fas fa-map-marker-alt'],
            ];
            foreach ($stats as $i => $s):
            ?>
            <div class="col-6 col-md-3" style="padding:30px 20px;<?= $i<3?'border-right:1px solid #eee;':'' ?>">
                <div style="font-size:28px;color:var(--green-primary);margin-bottom:10px;"><i class="<?= $s['icon'] ?>"></i></div>
                <div style="font-size:2.2rem;font-weight:800;color:var(--green-dark);font-family:'Playfair Display',serif;"><?= $s['num'] ?></div>
                <div style="font-size:13px;color:#888;margin-top:4px;font-weight:500;"><?= $s['label'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Our Mission & Values -->
<section class="py-5 bg-green-pale">
    <div class="container">
        <div class="text-center mb-5">
            <p style="color:var(--gold);font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:600;">What We Stand For</p>
            <h2 class="section-title">Our Mission & Values</h2>
            <div class="section-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            <?php
            $values = [
                ['icon'=>'fas fa-award','title'=>'Uncompromising Quality','color'=>'var(--green-primary)',
                 'desc'=>'Every fabric that leaves our store has been personally inspected for quality. We source only from certified weavers and manufacturers across Pakistan.'],
                ['icon'=>'fas fa-handshake','title'=>'Honest Pricing','color'=>'var(--gold)',
                 'desc'=>'Whether you buy one meter or a thousand, our prices are fair and transparent. No hidden charges, no haggling — just honest business.'],
                ['icon'=>'fas fa-leaf','title'=>'Authentic Heritage','color'=>'#2d9e6e',
                 'desc'=>'We celebrate Pakistani textile heritage. Our Banarsi, lawn, and silk collections are sourced from generations-old master weavers.'],
                ['icon'=>'fas fa-truck','title'=>'Reliable Delivery','color'=>'#1967d2',
                 'desc'=>'We deliver across all major cities in Pakistan within 3-5 business days. Each order is carefully packed to protect the fabric.'],
                ['icon'=>'fas fa-undo','title'=>'Easy Returns','color'=>'#dc3545',
                 'desc'=>'Not satisfied? We offer a 7-day hassle-free return policy. Your trust matters more to us than a single transaction.'],
                ['icon'=>'fas fa-headset','title'=>'Always Available','color'=>'#9334ea',
                 'desc'=>'Our customer support team is available 7 days a week via WhatsApp, phone, and email to assist with your queries.'],
            ];
            foreach ($values as $v):
            ?>
            <div class="col-md-6 col-lg-4">
                <div style="background:#fff;border-radius:16px;padding:28px;box-shadow:var(--shadow);height:100%;transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="width:56px;height:56px;background:<?= $v['color'] ?>18;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
                        <i class="<?= $v['icon'] ?>" style="font-size:22px;color:<?= $v['color'] ?>;"></i>
                    </div>
                    <h5 style="font-size:1.05rem;margin-bottom:10px;color:var(--text-dark);"><?= $v['title'] ?></h5>
                    <p style="font-size:14px;color:var(--text-light);line-height:1.8;margin:0;"><?= $v['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div style="position:relative;">
                    <div style="border-radius:20px;overflow:hidden;height:450px;box-shadow:var(--shadow-hover);">
                        <img src="images/products/p101.jfif" style="width:100%;height:100%;object-fit:cover;" alt="Our Fabric">
                    </div>
                    <div style="position:absolute;bottom:-20px;right:-20px;background:var(--gold);border-radius:14px;padding:20px 24px;box-shadow:var(--shadow-hover);text-align:center;">
                        <div style="font-size:2rem;font-weight:800;color:var(--green-dark);font-family:'Playfair Display',serif;">15+</div>
                        <div style="font-size:12px;font-weight:600;color:var(--green-dark);letter-spacing:0.5px;">Years Trusted</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <p style="color:var(--gold);font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:600;margin-bottom:12px;">Why Choose J&B</p>
                <h2 class="section-title mb-3">Manufacturer • Wholesaler • Retailer</h2>
                <p style="color:var(--text-light);font-size:15px;line-height:1.8;margin-bottom:28px;">
                    Unlike ordinary fabric stores, J&B controls the entire supply chain — from manufacturing to retail. This means better quality control, lower prices, and a wider variety exclusively for our customers.
                </p>
                <div class="d-flex flex-column gap-3">
                    <?php
                    $points = [
                        ['icon'=>'fas fa-industry','title'=>'Direct from Manufacturer','desc'=>'We manufacture our own fabrics, cutting out middlemen and passing savings to you.'],
                        ['icon'=>'fas fa-boxes','title'=>'Largest Collection in Karachi','desc'=>'500+ fabric varieties in stock, updated weekly with new arrivals from across Pakistan.'],
                        ['icon'=>'fas fa-certificate','title'=>'100% Authenticity Guarantee','desc'=>'Every fabric is authenticated. No imitations, blends sold as pure, or misleading descriptions.'],
                        ['icon'=>'fas fa-tags','title'=>'Best Wholesale Rates','desc'=>'Special bulk pricing available for tailors, boutiques, and fabric resellers.'],
                    ];
                    foreach ($points as $pt):
                    ?>
                    <div style="display:flex;gap:16px;align-items:flex-start;">
                        <div style="width:44px;height:44px;background:var(--green-pale);border-radius:12px;display:flex;align-items:center;justify-content:center;color:var(--green-primary);font-size:17px;flex-shrink:0;">
                            <i class="<?= $pt['icon'] ?>"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:15px;margin-bottom:3px;"><?= $pt['title'] ?></div>
                            <div style="font-size:13.5px;color:var(--text-light);"><?= $pt['desc'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5 bg-green-pale">
    <div class="container">
        <div class="text-center mb-5">
            <p style="color:var(--gold);font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:600;">The People Behind J&B</p>
            <h2 class="section-title">Meet Our Team</h2>
            <div class="section-divider mx-auto"></div>
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            $team = [
                ['name'=>'Jawad Ansari','role'=>'Founder & CEO','initial'=>'J','color'=>'var(--gold)'],
                ['name'=>'Hammad Ansari','role'=>'Co-Founder & Operations','initial'=>'H','color'=>'var(--gold)'],
                ['name'=>'Sajjad Ansari','role'=>'Head of Quality Control','initial'=>'S','color'=>'var(--gold)'],
                
            ];
            foreach ($team as $m):
            ?>
            <div class="col-6 col-md-3">
                <div style="background:#fff;border-radius:16px;padding:30px 20px;text-align:center;box-shadow:var(--shadow);transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="width:80px;height:80px;background:<?= $m['color'] ?>18;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;border:3px solid <?= $m['color'] ?>40;">
                        <span style="font-size:2rem;font-weight:800;color:<?= $m['color'] ?>;font-family:'Playfair Display',serif;"><?= $m['initial'] ?></span>
                    </div>
                    <div style="font-weight:700;font-size:15px;margin-bottom:4px;"><?= $m['name'] ?></div>
                    <div style="font-size:12.5px;color:var(--text-light);"><?= $m['role'] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section style="background:var(--green-dark);padding:60px 0;text-align:center;color:#fff;">
    <div class="container">
        <h2 style="color:#fff;font-size:2rem;margin-bottom:12px;">Ready to Experience J&B Quality?</h2>
        <p style="color:rgba(255,255,255,0.8);margin-bottom:30px;font-size:16px;">Visit our store or shop online and discover why thousands trust J&B.</p>
        <a href="shop.php" class="btn-hero-primary me-3"><i class="fas fa-shopping-bag me-2"></i>Shop Now</a>
        <a href="contact.php" class="btn-hero-secondary">Contact Us</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
