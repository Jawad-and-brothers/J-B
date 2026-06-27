<?php
$pageTitle = 'Contact Us';
require_once 'config.php';

$success = false;
$errors = [];
$vals = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $vals = compact('name','email','phone','subject','message');

    if (strlen($name) < 3)                        $errors['name']    = 'Please enter your full name (min 3 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']   = 'Please enter a valid email address.';
    if (!preg_match('/^[0-9+\-\s]{10,15}$/', $phone)) $errors['phone'] = 'Please enter a valid phone number.';
    if (!$subject)                                 $errors['subject'] = 'Please select a subject.';
    if (strlen($message) < 10)                     $errors['message'] = 'Message must be at least 10 characters.';

    if (empty($errors)) {
        // In a real app you'd send an email here via mail() or SMTP
        // For now we just show success
        $success = true;
        $vals = [];
    }
}
?>
<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2>Contact Us</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Map Strip -->
<div style="height:300px;background:var(--green-dark);position:relative;overflow:hidden;">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3619.6!2d67.0099!3d24.8607!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zKarachi+Cloth+Market!5e0!3m2!1sen!2spk!4v1"
        width="100%" height="300" style="border:0;filter:grayscale(30%);" allowfullscreen="" loading="lazy">
    </iframe>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">

            <!-- Contact Info Cards -->
            <div class="col-lg-4">
                <div class="text-center text-lg-start mb-4">
                    <p style="color:var(--gold);font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:600;">Get In Touch</p>
                    <h2 class="section-title">We'd Love to<br>Hear From You</h2>
                    <div class="section-divider" style="margin:10px 0 20px;"></div>
                    <p style="color:var(--text-light);font-size:15px;line-height:1.8;">Have questions about our fabrics or need a custom order? Reach out and our team will get back to you within 24 hours.</p>
                </div>

                <div class="d-flex flex-column gap-3">
                    <!-- Address -->
                    <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:var(--shadow);display:flex;gap:16px;align-items:flex-start;">
                        <div style="width:48px;height:48px;background:var(--green-pale);border-radius:12px;display:flex;align-items:center;justify-content:center;color:var(--green-primary);font-size:20px;flex-shrink:0;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;margin-bottom:4px;">Visit Our Store</div>
                            <div style="font-size:13.5px;color:var(--text-light);line-height:1.6;">Karachi Cloth Market,<br>Jodia Bazar, Karachi, Pakistan</div>
                        </div>
                    </div>
                    <!-- Phone -->
                    <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:var(--shadow);display:flex;gap:16px;align-items:flex-start;">
                        <div style="width:48px;height:48px;background:var(--green-pale);border-radius:12px;display:flex;align-items:center;justify-content:center;color:var(--green-primary);font-size:20px;flex-shrink:0;">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;margin-bottom:4px;">Call or WhatsApp</div>
                            <div style="font-size:13.5px;color:var(--text-light);line-height:1.8;">+92-311-1729783<br>+92-345-2729783</div>
                        </div>
                    </div>
                    <!-- Email -->
                    <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:var(--shadow);display:flex;gap:16px;align-items:flex-start;">
                        <div style="width:48px;height:48px;background:var(--green-pale);border-radius:12px;display:flex;align-items:center;justify-content:center;color:var(--green-primary);font-size:20px;flex-shrink:0;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;margin-bottom:4px;">Email Us</div>
                            <div style="font-size:13.5px;color:var(--text-light);line-height:1.8;">Ansari.Jawad89@gmail.com</div>
                        </div>
                    </div>
                    <!-- Hours -->
                    <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:var(--shadow);display:flex;gap:16px;align-items:flex-start;">
                        <div style="width:48px;height:48px;background:var(--green-pale);border-radius:12px;display:flex;align-items:center;justify-content:center;color:var(--green-primary);font-size:20px;flex-shrink:0;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;margin-bottom:4px;">Business Hours</div>
                            <div style="font-size:13.5px;color:var(--text-light);line-height:1.8;">Mon – Sat:11:00 AM – 8:00 PM</div>
                        </div>
                    </div>

                    <!-- Social -->
                    <div style="background:var(--green-dark);border-radius:14px;padding:20px 22px;">
                        <div style="font-weight:700;font-size:14px;color:#fff;margin-bottom:12px;">Follow Us</div>
                        <div class="d-flex gap-2">
                            <a href="#" style="width:40px;height:40px;background:rgba(255,255,255,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;transition:all 0.3s;" onmouseover="this.style.background='#1877f2'" onmouseout="this.style.background='rgba(255,255,255,0.1)'"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" style="width:40px;height:40px;background:rgba(255,255,255,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;transition:all 0.3s;" onmouseover="this.style.background='#e1306c'" onmouseout="this.style.background='rgba(255,255,255,0.1)'"><i class="fab fa-instagram"></i></a>
                            <a href="#" style="width:40px;height:40px;background:rgba(255,255,255,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;transition:all 0.3s;" onmouseover="this.style.background='#25d366'" onmouseout="this.style.background='rgba(255,255,255,0.1)'"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" style="width:40px;height:40px;background:rgba(255,255,255,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;transition:all 0.3s;" onmouseover="this.style.background='#000'" onmouseout="this.style.background='rgba(255,255,255,0.1)'"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-8">
                <div style="background:#fff;border-radius:16px;box-shadow:var(--shadow);padding:40px;">
                    <h4 style="font-size:1.4rem;margin-bottom:6px;">Send Us a Message</h4>
                    <p style="color:var(--text-light);font-size:14px;margin-bottom:28px;">Fill out the form below and we'll respond within 24 hours.</p>

                    <?php if ($success): ?>
                    <div style="text-align:center;padding:50px 20px;">
                        <div style="width:80px;height:80px;background:var(--green-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:36px;color:var(--green-primary);">
                            <i class="fas fa-check"></i>
                        </div>
                        <h4 style="color:var(--green-dark);margin-bottom:10px;">Message Sent Successfully!</h4>
                        <p style="color:var(--text-light);margin-bottom:24px;">Thank you for contacting us. Our team will get back to you within 24 hours.</p>
                        <a href="contact.php" class="btn-green d-inline-block px-5 py-2" style="text-decoration:none;border-radius:8px;width:auto;">Send Another Message</a>
                    </div>
                    <?php else: ?>

                    <form method="POST" action="contact.php" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control <?= isset($errors['name'])?'is-invalid':'' ?>"
                                    value="<?= sanitize($vals['name'] ?? (isLoggedIn() ? $_SESSION['user_name'] : '')) ?>"
                                    placeholder="Muhammad Ahmad" required>
                                <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= $errors['name'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control <?= isset($errors['email'])?'is-invalid':'' ?>"
                                    value="<?= sanitize($vals['email'] ?? (isLoggedIn() ? $_SESSION['user_email'] : '')) ?>"
                                    placeholder="you@example.com" required>
                                <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= $errors['email'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <input type="tel" name="phone" class="form-control <?= isset($errors['phone'])?'is-invalid':'' ?>"
                                    value="<?= sanitize($vals['phone'] ?? '') ?>" placeholder="0300-0000000" required>
                                <?php if (isset($errors['phone'])): ?><div class="invalid-feedback"><?= $errors['phone'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject *</label>
                                <select name="subject" class="form-select <?= isset($errors['subject'])?'is-invalid':'' ?>" required>
                                    <option value="">-- Select Subject --</option>
                                    <option value="Product Inquiry" <?= ($vals['subject']??'')==='Product Inquiry'?'selected':'' ?>>Product Inquiry</option>
                                    <option value="Wholesale Order" <?= ($vals['subject']??'')==='Wholesale Order'?'selected':'' ?>>Wholesale Order</option>
                                    <option value="Custom Order" <?= ($vals['subject']??'')==='Custom Order'?'selected':'' ?>>Custom Order</option>
                                    <option value="Order Status" <?= ($vals['subject']??'')==='Order Status'?'selected':'' ?>>Order Status</option>
                                    <option value="Return / Complaint" <?= ($vals['subject']??'')==='Return / Complaint'?'selected':'' ?>>Return / Complaint</option>
                                    <option value="Other" <?= ($vals['subject']??'')==='Other'?'selected':'' ?>>Other</option>
                                </select>
                                <?php if (isset($errors['subject'])): ?><div class="invalid-feedback"><?= $errors['subject'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message *</label>
                                <textarea name="message" rows="6" class="form-control <?= isset($errors['message'])?'is-invalid':'' ?>"
                                    placeholder="Tell us about your inquiry, order details, or any questions you have..."
                                    required><?= sanitize($vals['message'] ?? '') ?></textarea>
                                <?php if (isset($errors['message'])): ?><div class="invalid-feedback"><?= $errors['message'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-green py-3 px-5" style="width:auto;border-radius:8px;font-size:15px;">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>

                <!-- WhatsApp CTA -->
                <div style="background:linear-gradient(135deg,#128c7e,#25d366);border-radius:14px;padding:24px 28px;margin-top:20px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                    <div style="font-size:40px;color:#fff;"><i class="fab fa-whatsapp"></i></div>
                    <div style="flex:1;">
                        <div style="font-weight:700;font-size:16px;color:#fff;margin-bottom:4px;">Prefer WhatsApp?</div>
                        <div style="font-size:14px;color:rgba(255,255,255,0.85);">Chat with us directly for fast responses on orders, fabric availability and pricing.</div>
                    </div>
                    <a href="https://wa.me/923000000000" target="_blank"
                        style="background:#fff;color:#128c7e;font-weight:700;padding:12px 24px;border-radius:50px;font-size:14px;text-decoration:none;white-space:nowrap;transition:all 0.3s;"
                        onmouseover="this.style.background='#f0fff4'" onmouseout="this.style.background='#fff'">
                        Chat on WhatsApp
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>