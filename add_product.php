<?php
$adminTitle = 'Add New Product';
require_once 'admin_config.php';
requireAdmin();
$db = getDB();

$errors = [];
$vals = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $vals = compact('name','category_id','description','price','stock','featured');

    if (!$name) $errors['name'] = 'Product name is required.';
    if (!$category_id) $errors['category_id'] = 'Please select a category.';
    if ($price <= 0) $errors['price'] = 'Please enter a valid price.';
    if ($stock < 0) $errors['stock'] = 'Stock cannot be negative.';

    // Handle image upload
    $image_filename = '';
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed)) {
            $errors['image'] = 'Only JPG, PNG, WEBP images are allowed.';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $errors['image'] = 'Image must be under 5MB.';
        } else {
            $image_filename = 'p_' . time() . '_' . rand(100,999) . '.' . $ext;
            $dest = '../images/products/' . $image_filename;
            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                $errors['image'] = 'Failed to upload image. Check folder permissions.';
                $image_filename = '';
            }
        }
    } else {
        $errors['image'] = 'Please upload a product image.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO products (category_id, name, description, price, stock, image, featured) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param('issdisd', $category_id, $name, $description, $price, $stock, $image_filename, $featured);
        if ($stmt->execute()) {
            $_SESSION['admin_msg'] = ['type'=>'success','text'=>"Product '$name' added successfully!"];
            header('Location: products.php'); exit;
        } else {
            $errors['general'] = 'Database error. Please try again.';
        }
    }
}

$categories = $db->query("SELECT * FROM categories ORDER BY name");
// Get existing images for selection
$existingImages = glob('../images/products/*.jpeg') ?: [];
$existingImages = array_merge($existingImages, glob('../images/products/*.jpg') ?: []);
$existingImages = array_merge($existingImages, glob('../images/products/*.png') ?: []);
sort($existingImages);
?>
<?php include 'includes/sidebar.php'; ?>
<div class="page-body">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="products.php" style="color:var(--text-mid);font-size:18px;"><i class="fas fa-arrow-left"></i></a>
    <h4 style="margin:0;font-size:1.3rem;">Add New Product</h4>
</div>

<?php if (!empty($errors['general'])): ?>
<div class="alert-admin error"><i class="fas fa-exclamation-circle me-2"></i><?= $errors['general'] ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
<div class="row g-4">

    <!-- Left Column -->
    <div class="col-lg-8">
        <div class="form-card mb-4">
            <div class="form-section-title"><i class="fas fa-info-circle me-2"></i>Product Information</div>
            <div class="mb-3">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" class="form-control <?= isset($errors['name'])?'is-invalid':'' ?>" value="<?= sanitize($vals['name'] ?? '') ?>" placeholder="e.g. Golden Floral Banarsi Fabric" required>
                <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= $errors['name'] ?></div><?php endif; ?>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-select <?= isset($errors['category_id'])?'is-invalid':'' ?>" required>
                        <option value="">-- Select Category --</option>
                        <?php while($c=$categories->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>" <?= (($vals['category_id'] ?? 0)==$c['id'])?'selected':'' ?>><?= sanitize($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <?php if (isset($errors['category_id'])): ?><div class="invalid-feedback"><?= $errors['category_id'] ?></div><?php endif; ?>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Price (PKR) *</label>
                    <input type="number" name="price" class="form-control <?= isset($errors['price'])?'is-invalid':'' ?>" value="<?= $vals['price'] ?? '' ?>" placeholder="2500" min="0" step="50" required>
                    <?php if (isset($errors['price'])): ?><div class="invalid-feedback"><?= $errors['price'] ?></div><?php endif; ?>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Stock (meters)</label>
                    <input type="number" name="stock" class="form-control" value="<?= $vals['stock'] ?? '0' ?>" placeholder="10" min="0" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Describe the fabric — material, design, suitable occasions..."><?= sanitize($vals['description'] ?? '') ?></textarea>
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="featured" id="featuredCheck" <?= !empty($vals['featured'])?'checked':'' ?>>
                <label class="form-check-label" for="featuredCheck" style="font-size:13.5px;font-weight:600;">
                    ⭐ Mark as Featured Product (shows on homepage)
                </label>
            </div>
        </div>
    </div>

    <!-- Right Column: Image -->
    <div class="col-lg-4">
        <div class="form-card mb-4">
            <div class="form-section-title"><i class="fas fa-image me-2"></i>Product Image</div>

            <!-- Upload new image -->
            <div class="mb-3">
                <label class="form-label">Upload New Image</label>
                <div class="img-preview-box" onclick="document.getElementById('imgUpload').click()" id="imgPreviewBox">
                    <div class="placeholder">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span style="font-size:13px;">Click to upload<br><small>JPG, PNG, WEBP • Max 5MB</small></span>
                    </div>
                </div>
                <input type="file" name="image" id="imgUpload" accept="image/*" style="display:none;" onchange="previewImage(this)">
                <?php if (isset($errors['image'])): ?><div class="text-danger mt-1" style="font-size:12.5px;"><?= $errors['image'] ?></div><?php endif; ?>
            </div>

            <div class="divider-or d-flex align-items-center gap-2 my-3" style="color:#888;font-size:12px;">
                <div style="flex:1;height:1px;background:#eee;"></div>OR choose existing<div style="flex:1;height:1px;background:#eee;"></div>
            </div>

            <!-- Choose from existing -->
            <div>
                <label class="form-label">Use Existing Image</label>
                <select name="existing_image" class="form-select" id="existingImgSelect" onchange="previewExisting(this.value)">
                    <option value="">-- Select existing image --</option>
                    <?php foreach ($existingImages as $img):
                        $fname = basename($img); ?>
                    <option value="<?= $fname ?>"><?= $fname ?></option>
                    <?php endforeach; ?>
                </select>
                <div id="existingPreview" class="mt-2" style="display:none;">
                    <img id="existingPreviewImg" src="" style="width:100%;height:120px;object-fit:cover;border-radius:8px;">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-admin-primary w-100 py-3" style="font-size:15px;border-radius:10px;">
            <i class="fas fa-plus-circle me-2"></i>Add Product
        </button>
        <a href="products.php" style="display:block;text-align:center;margin-top:12px;color:#888;font-size:13px;">Cancel</a>
    </div>

</div>
</form>

</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const box = document.getElementById('imgPreviewBox');
            box.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
        // Clear existing selection
        document.getElementById('existingImgSelect').value = '';
        document.getElementById('existingPreview').style.display = 'none';
    }
}
function previewExisting(val) {
    const preview = document.getElementById('existingPreview');
    const img = document.getElementById('existingPreviewImg');
    if (val) {
        img.src = '../images/products/' + val;
        preview.style.display = 'block';
        // Clear file upload preview
        document.getElementById('imgPreviewBox').innerHTML = `<div class="placeholder"><i class="fas fa-cloud-upload-alt" style="font-size:36px;margin-bottom:8px;display:block;"></i><span style="font-size:13px;">Click to upload<br><small>JPG, PNG, WEBP • Max 5MB</small></span></div>`;
    } else {
        preview.style.display = 'none';
    }
}
</script>
<?php include 'includes/footer.php'; ?>