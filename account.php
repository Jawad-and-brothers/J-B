<?php
$pageTitle = 'My Account';
require_once 'config.php';
requireLogin();
$db = getDB();
$user_id = (int)$_SESSION['user_id'];

// Fetch fresh user data
$user = $db->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

$success = '';
$errors  = [];

// ---- Update Profile ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'update_profile') {
        $full_name = trim($_POST['full_name'] ?? '');
        $phone     = trim($_POST['phone'] ?? '');
        $address   = trim($_POST['address'] ?? '');
        $city      = trim($_POST['city'] ?? '');

        if (strlen($full_name) < 3) $errors['full_name'] = 'Name must be at least 3 characters.';
        if (!preg_match('/^[0-9+\-\s]{10,15}$/', $phone)) $errors['phone'] = 'Please enter a valid phone number.';

        if (empty($errors)) {
            $stmt = $db->prepare("UPDATE users SET full_name=?, phone=?, address=?, city=? WHERE id=?");
            $stmt->bind_param('ssssi', $full_name, $phone, $address, $city, $user_id);
            if ($stmt->execute()) {
                $_SESSION['user_name'] = $full_name;
                $success = 'profile';
                $user = $db->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
            } else { $errors['general'] = 'Update failed. Please try again.'; }
        }
    }

    if ($_POST['action'] === 'change_password') {
        $current  = $_POST['current_password'] ?? '';
        $new      = $_POST['new_password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $user['password'])) {
            $errors['current_password'] = 'Current password is incorrect.';
        } elseif (strlen($new) < 8) {
            $errors['new_password'] = 'New password must be at least 8 characters.';
        } elseif (!preg_match('/[A-Z]/', $new)) {
            $errors['new_password'] = 'Password must contain at least one uppercase letter.';
        } elseif (!preg_match('/[0-9]/', $new)) {
            $errors['new_password'] = 'Password must contain at least one number.';
        } elseif ($new !== $confirm) {
            $errors['confirm_password'] = 'Passwords do not match.';
        } else {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param('si', $hashed, $user_id);
            if ($stmt->execute()) { $success = 'password'; }
            else { $errors['general'] = 'Password update failed.'; }
        }
    }
}

// Fetch order stats
$orderStats = $db->query("SELECT
    COUNT(*) as total,
    SUM(total_amount) as total_spent,
    SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status='delivered' THEN 1 ELSE 0 END) as delivered
    FROM orders WHERE user_id=$user_id")->fetch_assoc();

// Recent orders
$recentOrders = $db->query("SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC LIMIT 5");

$activeTab = $_GET['tab'] ?? 'profile';
?>
<?php include 'includes/header.php'; ?>

<div class="page-header">
    <div class="container">
        <h2>My Account</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">My Account</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
<div class="container">

    <!-- Profile Header Card -->
    <div style="background:linear-gradient(135deg,var(--green-dark),var(--green-primary));border-radius:20px;padding:32px;margin-bottom:28px;color:#fff;display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
        <div style="width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.2rem;font-weight:800;font-family:'Playfair Display',serif;flex-shrink:0;border:3px solid rgba(255,255,255,0.3);">
            <?= strtoupper($user['full_name'][0]) ?>
        </div>
        <div style="flex:1;">
            <h3 style="color:#fff;margin-bottom:4px;font-size:1.5rem;"><?= sanitize($user['full_name']) ?></h3>
            <div style="color:rgba(255,255,255,0.75);font-size:14px;"><i class="fas fa-envelope me-2"></i><?= sanitize($user['email']) ?></div>
            <div style="color:rgba(255,255,255,0.75);font-size:14px;margin-top:4px;"><i class="fas fa-calendar-alt me-2"></i>Member since <?= date('F Y', strtotime($user['created_at'])) ?></div>
        </div>
        <!-- Quick Stats -->
        <div style="display:flex;gap:20px;flex-wrap:wrap;">
            <div style="text-align:center;background:rgba(255,255,255,0.1);border-radius:12px;padding:14px 20px;">
                <div style="font-size:1.8rem;font-weight:800;font-family:'Playfair Display',serif;"><?= (int)$orderStats['total'] ?></div>
                <div style="font-size:11px;color:rgba(255,255,255,0.7);letter-spacing:0.5px;">ORDERS</div>
            </div>
            <div style="text-align:center;background:rgba(255,255,255,0.1);border-radius:12px;padding:14px 20px;">
                <div style="font-size:1.8rem;font-weight:800;font-family:'Playfair Display',serif;"><?= (int)$orderStats['delivered'] ?></div>
                <div style="font-size:11px;color:rgba(255,255,255,0.7);letter-spacing:0.5px;">DELIVERED</div>
            </div>
            <div style="text-align:center;background:rgba(201,168,76,0.25);border-radius:12px;padding:14px 20px;border:1px solid rgba(201,168,76,0.4);">
                <div style="font-size:1.3rem;font-weight:800;color:var(--gold-light);">PKR <?= number_format((float)$orderStats['total_spent'],0) ?></div>
                <div style="font-size:11px;color:rgba(255,255,255,0.7);letter-spacing:0.5px;">TOTAL SPENT</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Tabs -->
        <div class="col-lg-3">
            <div style="background:#fff;border-radius:16px;box-shadow:var(--shadow);overflow:hidden;">
                <?php
                $tabs = [
                    'profile'  => ['icon'=>'fas fa-user','label'=>'Edit Profile'],
                    'password' => ['icon'=>'fas fa-lock','label'=>'Change Password'],
                    'orders'   => ['icon'=>'fas fa-box','label'=>'My Orders'],
                ];
                foreach ($tabs as $key => $tab):
                    $isActive = $activeTab === $key;
                ?>
                <a href="account.php?tab=<?= $key ?>"
                   style="display:flex;align-items:center;gap:12px;padding:15px 20px;border-bottom:1px solid #f5f5f5;font-weight:600;font-size:14px;color:<?= $isActive?'var(--green-primary)':'var(--text-mid)' ?>;background:<?= $isActive?'var(--green-pale)':'transparent' ?>;transition:all 0.2s;"
                   onmouseover="this.style.background='var(--green-pale)'" onmouseout="this.style.background='<?= $isActive?'var(--green-pale)':'transparent' ?>'">
                    <i class="<?= $tab['icon'] ?>" style="width:18px;text-align:center;"></i>
                    <?= $tab['label'] ?>
                    <?php if ($key==='orders' && (int)$orderStats['pending'] > 0): ?>
                    <span style="margin-left:auto;background:var(--gold);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:50px;"><?= $orderStats['pending'] ?></span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
                <a href="cart.php" style="display:flex;align-items:center;gap:12px;padding:15px 20px;border-bottom:1px solid #f5f5f5;font-weight:600;font-size:14px;color:var(--text-mid);transition:all 0.2s;" onmouseover="this.style.background='var(--green-pale)'" onmouseout="this.style.background='transparent'">
                    <i class="fas fa-shopping-cart" style="width:18px;text-align:center;"></i>My Cart
                </a>
                <a href="logout.php" style="display:flex;align-items:center;gap:12px;padding:15px 20px;font-weight:600;font-size:14px;color:#dc3545;transition:all 0.2s;" onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='transparent'">
                    <i class="fas fa-sign-out-alt" style="width:18px;text-align:center;"></i>Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">

            <!-- ======= PROFILE TAB ======= -->
            <?php if ($activeTab === 'profile'): ?>
            <div style="background:#fff;border-radius:16px;box-shadow:var(--shadow);padding:32px;">
                <h5 style="font-size:1.2rem;margin-bottom:6px;color:var(--green-dark);">Edit Profile Information</h5>
                <p style="color:var(--text-light);font-size:13.5px;margin-bottom:24px;">Update your personal details below.</p>

                <?php if ($success === 'profile'): ?>
                <div class="alert alert-success d-flex align-items-center mb-4" style="font-size:14px;">
                    <i class="fas fa-check-circle me-2"></i> Profile updated successfully!
                </div>
                <?php endif; ?>
                <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger mb-4"><?= $errors['general'] ?></div>
                <?php endif; ?>

                <form method="POST" action="account.php?tab=profile" novalidate>
                    <input type="hidden" name="action" value="update_profile">
                    <div class="row g-3">
                        <!-- Read-only Email -->
                        <div class="col-12">
                            <label class="form-label">Email Address <span style="font-size:11px;color:#aaa;font-weight:400;">(cannot be changed)</span></label>
                            <div style="display:flex;align-items:center;gap:10px;background:#f8f9fa;border:1.5px solid #e9ecef;border-radius:8px;padding:11px 14px;">
                                <i class="fas fa-envelope" style="color:#aaa;"></i>
                                <span style="font-size:14px;color:#666;"><?= sanitize($user['email']) ?></span>
                                <span style="margin-left:auto;font-size:11px;background:#e8f0eb;color:var(--green-primary);padding:2px 10px;border-radius:50px;font-weight:600;">Verified</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="full_name" class="form-control <?= isset($errors['full_name'])?'is-invalid':'' ?>"
                                value="<?= sanitize($user['full_name']) ?>" required>
                            <?php if (isset($errors['full_name'])): ?><div class="invalid-feedback"><?= $errors['full_name'] ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" name="phone" class="form-control <?= isset($errors['phone'])?'is-invalid':'' ?>"
                                value="<?= sanitize($user['phone']) ?>" placeholder="0300-0000000" required>
                            <?php if (isset($errors['phone'])): ?><div class="invalid-feedback"><?= $errors['phone'] ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control"
                                value="<?= sanitize($user['address'] ?? '') ?>" placeholder="Street, area, block...">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <select name="city" class="form-select">
                                <option value="">Select City</option>
                                <?php foreach(['Karachi','Lahore','Islamabad','Rawalpindi','Peshawar','Quetta','Faisalabad','Multan','Hyderabad','Other'] as $c): ?>
                                <option value="<?= $c ?>" <?= ($user['city']===$c)?'selected':'' ?>><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 pt-2">
                            <button type="submit" class="btn-green" style="width:auto;padding:12px 40px;border-radius:8px;font-size:15px;">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ======= PASSWORD TAB ======= -->
            <?php elseif ($activeTab === 'password'): ?>
            <div style="background:#fff;border-radius:16px;box-shadow:var(--shadow);padding:32px;">
                <h5 style="font-size:1.2rem;margin-bottom:6px;color:var(--green-dark);">Change Password</h5>
                <p style="color:var(--text-light);font-size:13.5px;margin-bottom:24px;">Choose a strong password to keep your account secure.</p>

                <?php if ($success === 'password'): ?>
                <div class="alert alert-success d-flex align-items-center mb-4" style="font-size:14px;">
                    <i class="fas fa-check-circle me-2"></i> Password changed successfully!
                </div>
                <?php endif; ?>

                <form method="POST" action="account.php?tab=password" novalidate style="max-width:480px;">
                    <input type="hidden" name="action" value="change_password">

                    <div class="mb-3">
                        <label class="form-label">Current Password *</label>
                        <div class="password-wrap">
                            <input type="password" name="current_password" class="form-control <?= isset($errors['current_password'])?'is-invalid':'' ?>" placeholder="Your current password" required>
                            <button type="button" class="password-toggle"><i class="fas fa-eye"></i></button>
                        </div>
                        <?php if (isset($errors['current_password'])): ?><div class="invalid-feedback d-block"><?= $errors['current_password'] ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password *</label>
                        <div class="password-wrap">
                            <input type="password" name="new_password" id="newPw" class="form-control <?= isset($errors['new_password'])?'is-invalid':'' ?>" placeholder="Min 8 chars, 1 uppercase, 1 number" required>
                            <button type="button" class="password-toggle"><i class="fas fa-eye"></i></button>
                        </div>
                        <?php if (isset($errors['new_password'])): ?><div class="invalid-feedback d-block"><?= $errors['new_password'] ?></div><?php endif; ?>
                        <div id="pwStrength" class="mt-1" style="font-size:12px;"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Confirm New Password *</label>
                        <div class="password-wrap">
                            <input type="password" name="confirm_password" class="form-control <?= isset($errors['confirm_password'])?'is-invalid':'' ?>" placeholder="Repeat new password" required>
                            <button type="button" class="password-toggle"><i class="fas fa-eye"></i></button>
                        </div>
                        <?php if (isset($errors['confirm_password'])): ?><div class="invalid-feedback d-block"><?= $errors['confirm_password'] ?></div><?php endif; ?>
                    </div>

                    <div style="background:var(--green-pale);border-radius:8px;padding:12px 16px;font-size:13px;color:var(--green-dark);margin-bottom:20px;">
                        <i class="fas fa-shield-alt me-2"></i><strong>Password Requirements:</strong>
                        <ul style="margin:6px 0 0 20px;padding:0;">
                            <li>At least 8 characters</li>
                            <li>At least one uppercase letter (A-Z)</li>
                            <li>At least one number (0-9)</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn-green" style="width:auto;padding:12px 40px;border-radius:8px;font-size:15px;">
                        <i class="fas fa-key me-2"></i>Change Password
                    </button>
                </form>
            </div>

            <!-- ======= ORDERS TAB ======= -->
            <?php elseif ($activeTab === 'orders'): ?>
            <div style="background:#fff;border-radius:16px;box-shadow:var(--shadow);overflow:hidden;">
                <div style="padding:22px 26px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;">
                    <h5 style="font-size:1.1rem;margin:0;color:var(--green-dark);">My Orders</h5>
                    <span style="font-size:13px;color:#888;"><?= (int)$orderStats['total'] ?> total order<?= (int)$orderStats['total']!=1?'s':'' ?></span>
                </div>

                <?php
                $allOrders = $db->query("SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC");
                $statusColors = ['pending'=>['bg'=>'#fff3cd','c'=>'#856404'],'processing'=>['bg'=>'#cfe2ff','c'=>'#084298'],'shipped'=>['bg'=>'#d1ecf1','c'=>'#0c5460'],'delivered'=>['bg'=>'#d4edda','c'=>'#155724'],'cancelled'=>['bg'=>'#f8d7da','c'=>'#721c24']];
                if ($allOrders->num_rows == 0):
                ?>
                <div style="text-align:center;padding:60px 20px;color:#aaa;">
                    <i class="fas fa-box-open" style="font-size:60px;margin-bottom:16px;display:block;"></i>
                    <p style="font-size:15px;">You haven't placed any orders yet.</p>
                    <a href="shop.php" class="btn-green d-inline-block px-5 py-2 mt-2" style="text-decoration:none;border-radius:8px;width:auto;">Start Shopping</a>
                </div>
                <?php else: while ($o = $allOrders->fetch_assoc()):
                    $sc = $statusColors[$o['status']] ?? ['bg'=>'#eee','c'=>'#555'];
                    $items = $db->query("SELECT product_name, quantity FROM order_items WHERE order_id={$o['id']}");
                ?>
                <div style="border-bottom:1px solid #f5f5f5;padding:20px 26px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
                        <div>
                            <div style="font-weight:700;font-size:15px;color:var(--green-dark);"><?= sanitize($o['order_number']) ?></div>
                            <div style="font-size:12.5px;color:#aaa;margin-top:2px;"><i class="fas fa-calendar me-1"></i><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                            <span style="background:<?= $sc['bg'] ?>;color:<?= $sc['c'] ?>;font-size:12px;font-weight:600;padding:4px 14px;border-radius:50px;"><?= ucfirst($o['status']) ?></span>
                            <strong style="font-size:15px;color:var(--green-primary);">PKR <?= number_format($o['total_amount'],0) ?></strong>
                        </div>
                    </div>
                    <!-- Items list -->
                    <div style="background:#f9faf9;border-radius:8px;padding:12px 16px;margin-bottom:12px;">
                        <?php while ($item = $items->fetch_assoc()): ?>
                        <div style="font-size:13.5px;color:var(--text-mid);padding:3px 0;">
                            <i class="fas fa-circle" style="font-size:5px;color:#aaa;vertical-align:middle;margin-right:8px;"></i>
                            <?= sanitize($item['product_name']) ?> <span style="color:#aaa;">× <?= $item['quantity'] ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;font-size:13px;color:#888;">
                        <span><i class="fas fa-map-marker-alt me-1"></i><?= sanitize($o['shipping_city']) ?> &nbsp;|&nbsp; <i class="fas fa-credit-card me-1"></i><?= ucwords(str_replace('_',' ',$o['payment_method'])) ?></span>
                        <?php if ($o['status'] === 'pending'): ?>
                        <span style="color:var(--gold);font-size:12px;"><i class="fas fa-clock me-1"></i>Order is being processed</span>
                        <?php elseif ($o['status'] === 'shipped'): ?>
                        <span style="color:#0c5460;font-size:12px;"><i class="fas fa-truck me-1"></i>On the way!</span>
                        <?php elseif ($o['status'] === 'delivered'): ?>
                        <span style="color:var(--green-primary);font-size:12px;"><i class="fas fa-check-circle me-1"></i>Delivered</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
</section>

<script>
// Password strength meter
const newPwInput = document.getElementById('newPw');
const strengthDiv = document.getElementById('pwStrength');
if (newPwInput && strengthDiv) {
    newPwInput.addEventListener('input', function() {
        const v = this.value;
        let score = 0;
        if (v.length >= 8) score++;
        if (/[A-Z]/.test(v)) score++;
        if (/[0-9]/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;
        const labels = ['','Weak','Fair','Good','Strong'];
        const colors = ['','#dc3545','#fd7e14','#ffc107','var(--green-primary)'];
        strengthDiv.innerHTML = v.length > 0
            ? `<span style="color:${colors[score]};">Strength: <b>${labels[score]}</b></span>` : '';
    });
}
</script>

<?php include 'includes/footer.php'; ?>
