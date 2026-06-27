<?php
$pageTitle = 'Create Account';
require_once 'config.php';

if (isLoggedIn()) { header('Location: index.php'); exit; }

$errors = [];
$vals = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $agree = $_POST['agree'] ?? '';

    $vals = compact('full_name','email','phone','address','city');

    // Validations
    if (strlen($full_name) < 3) $errors['full_name'] = 'Full name must be at least 3 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email address.';
    if (!preg_match('/^[0-9+\-\s]{10,15}$/', $phone)) $errors['phone'] = 'Please enter a valid phone number (10-15 digits).';
    if (strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters.';
    if (!preg_match('/[A-Z]/', $password)) $errors['password'] = 'Password must contain at least one uppercase letter.';
    if (!preg_match('/[0-9]/', $password)) $errors['password'] = 'Password must contain at least one number.';
    if ($password !== $confirm) $errors['confirm_password'] = 'Passwords do not match.';
    if (!$agree) $errors['agree'] = 'You must agree to the Terms & Conditions.';

    if (empty($errors)) {
        $db = getDB();
        // Check if email exists
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors['email'] = 'This email address is already registered. <a href="login.php">Login instead?</a>';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (full_name, email, phone, password, address, city) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('ssssss', $full_name, $email, $phone, $hashed, $address, $city);
            if ($stmt->execute()) {
                $new_id = $stmt->insert_id;
                $_SESSION['user_id'] = $new_id;
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_email'] = $email;
                $_SESSION['flash_msg'] = 'Account created successfully! Welcome to J&B, ' . htmlspecialchars($full_name) . '!';
                $_SESSION['flash_type'] = 'success';
                header('Location: index.php');
                exit;
            } else {
                $errors['general'] = 'Registration failed. Please try again.';
            }
        }
        $check->close();
    }
}
?>
<?php include 'includes/header.php'; ?>

<section class="auth-page" style="background:linear-gradient(135deg,var(--green-pale) 0%,#fff 100%);padding:50px 0;">
    <div class="container">
        <div class="auth-card mx-auto" style="max-width:550px;">
            <div class="auth-logo"><img src="images/logo.png" alt="J&B Logo"></div>
            <h3 class="auth-title">Create Account</h3>
            <p class="auth-sub">Join thousands of satisfied J&B customers</p>

            <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger"><?= $errors['general'] ?></div>
            <?php endif; ?>

            <form id="registerForm" method="POST" action="register.php" novalidate>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="full_name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control <?= isset($errors['full_name'])?'is-invalid':'' ?>"
                            id="full_name" name="full_name" value="<?= sanitize($vals['full_name'] ?? '') ?>"
                            placeholder="Muhammad Ahmad" required>
                        <?php if (isset($errors['full_name'])): ?>
                        <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control <?= isset($errors['email'])?'is-invalid':'' ?>"
                            id="email" name="email" value="<?= sanitize($vals['email'] ?? '') ?>"
                            placeholder="you@example.com" required>
                        <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= $errors['email'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control <?= isset($errors['phone'])?'is-invalid':'' ?>"
                            id="phone" name="phone" value="<?= sanitize($vals['phone'] ?? '') ?>"
                            placeholder="0300-0000000" required>
                        <?php if (isset($errors['phone'])): ?>
                        <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <div class="password-wrap">
                            <input type="password" class="form-control <?= isset($errors['password'])?'is-invalid':'' ?>"
                                id="password" name="password" placeholder="Min 8 chars, 1 uppercase, 1 number" required>
                            <button type="button" class="password-toggle"><i class="fas fa-eye"></i></button>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback d-block"><?= $errors['password'] ?></div>
                        <?php endif; ?>
                        <div id="pwStrength" class="mt-1" style="font-size:12px;"></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                        <div class="password-wrap">
                            <input type="password" class="form-control <?= isset($errors['confirm_password'])?'is-invalid':'' ?>"
                                id="confirm_password" name="confirm_password" placeholder="Repeat your password" required>
                            <button type="button" class="password-toggle"><i class="fas fa-eye"></i></button>
                        </div>
                        <?php if (isset($errors['confirm_password'])): ?>
                        <div class="invalid-feedback d-block"><?= $errors['confirm_password'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-8 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address"
                            value="<?= sanitize($vals['address'] ?? '') ?>" placeholder="Street address (optional)">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="city" class="form-label">City</label>
                        <select class="form-select" id="city" name="city">
                            <option value="">Select City</option>
                            <?php foreach(['Karachi','Lahore','Islamabad','Rawalpindi','Peshawar','Quetta','Faisalabad','Multan','Hyderabad','Other'] as $c): ?>
                            <option value="<?= $c ?>" <?= (($vals['city'] ?? '') == $c)?'selected':'' ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input <?= isset($errors['agree'])?'is-invalid':'' ?>"
                                type="checkbox" id="agree" name="agree" value="1">
                            <label class="form-check-label" for="agree" style="font-size:13px;">
                                I agree to the <a href="#" style="color:var(--green-primary);">Terms & Conditions</a> and <a href="#" style="color:var(--green-primary);">Privacy Policy</a> *
                            </label>
                            <?php if (isset($errors['agree'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['agree'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-green">
                    <i class="fas fa-user-plus me-2"></i>Create My Account
                </button>
            </form>

            <div class="divider-or"><span>OR</span></div>
            <div class="text-center">
                <p style="font-size:14px;color:#555;">Already have an account?
                    <a href="login.php" style="color:var(--green-primary);font-weight:600;">Login</a>
                </p>
            </div>
        </div>
    </div>
</section>

<script>
// Live password strength
const pwInput = document.getElementById('password');
const pwStrength = document.getElementById('pwStrength');
if (pwInput && pwStrength) {
    pwInput.addEventListener('input', function () {
        const v = this.value;
        let score = 0;
        if (v.length >= 8) score++;
        if (/[A-Z]/.test(v)) score++;
        if (/[0-9]/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;
        const labels = ['','Weak','Fair','Good','Strong'];
        const colors = ['','#dc3545','#fd7e14','#ffc107','var(--green-primary)'];
        pwStrength.innerHTML = v.length > 0
            ? `<span style="color:${colors[score]};">Password strength: <b>${labels[score]}</b></span>` : '';
    });
}
</script>
<?php include 'includes/footer.php'; ?>