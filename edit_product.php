<?php
$adminTitle = 'Edit Product';
require_once 'admin_config.php';
requireAdmin();
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: products.php'); exit; }

$product = $db->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
if (!$product) { header('Location: products.php'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;

    if (!$name) $errors['name'] = 'Product name is required.';
    if (!$category_id) $errors['category_id'] = 'Please select a category.';
    if ($price <= 0) $errors['price'] = 'Please enter a valid price.';

    // Handle image
    $image_filename = $product['image']; // Keep existing by default

    // Check if existing image selected
    $existing_sel = trim($_POST['existing_image'] ?? '');
    if ($existing_sel && file_exists('../images/products/' . $existing_sel)) {
        $image_filename = $existing_sel;
    }

    // Check if new image uploaded
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed)) {
            $errors['image'] = 'Only JPG, PNG, WEBP images are allowed.';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $errors['image'] = 'Image must be under 5MB.';
        } else {
            $new_filename = 'p_' . time() . '_' . rand(100,999) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], '../images/products/' . $new_filename)) {
                $image_filename = $new_filename;
            } else {
                $errors['image'] = 'Failed to upload image.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, image=?, featured=? WHERE id=?");
        $stmt->bind_param('issdisii', $category_id, $name, $description, $price, $stock, $image_filename, $featured, $id);
        if ($stmt->execute()) {
            $_SESSION['admin_msg'] = ['type'=>'success','text'=>"Product '$name' updated successfully!"];
            header('Location: products.php'); exit;
        } else {
            $errors['general'] = 'Database error. Please try again.';
        }
    }
    // Update product array for re-display
    $product = array_merge($product, compact('name','category_id','description','price','stock','featured'));
    $product['image'] = $image_filename;
}

$categories = $db->query("SELECT * FROM categories ORDER BY name");
$existingImages = array_merge(glob('../images/products/*.jpeg') ?: [], glob('../images/products/*.jpg') ?: [], glob('../images/products/*.png') ?: []);
sort($existingImages);
?>
<?php include 'includes/sidebar.php'; ?>
<div class="page-body">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="products.php" style="color:var(--text-mid);font-size:18px;"><i class="fas fa-arrow-left"></i></a>
    <h4 style="margin:0;font-size:1.3rem;">Edit Product — <?= sanitize($product['name']) ?></h4>
</div>

<?php if (!empty($errors['general'])): ?>
<div class="alert-admin error"><i class="fas fa-exclamation-circle me-2"></i><?= $errors['general'] ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
<div class="row g-4">

    <div class="col-lg-8">
        <div class="form-card mb-4">
            <div class="form-section-title"><i class="fas fa-edit me-2"></i>Product Information</div>
            <div class="mb-3">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" class="form-control <?= isset($errors['name'])?'is-invalid':'' ?>" value="<?= sanitize($product['name']) ?>" required>
                <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= $errors['name'] ?></div><?php endif; ?>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-select <?= isset($errors['category_id'])?'is-invalid':'' ?>" required>
                        <option value="">-- Select --</option>
                        <?php while($c=$categories->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>" <?= $product['category_id']==$c['id']?'selected':'' ?>><?= sanitize($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Price (PKR) *</label>
                    <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" min="0" step="50" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Stock (meters)</label>
                    <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" min="0" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= sanitize($product['description']) ?></textarea>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="featured" id="featuredCheck" <?= $product['featured']?'checked':'' ?>>
                <label class="form-check-label" for="featuredCheck" style="font-size:13.5px;font-weight:600;">⭐ Mark as Featured Product</label>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-card mb-4">
            <div class="form-section-title"><i class="fas fa-image me-2"></i>Product Image</div>

            <!-- Current image -->
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;color:#888;">Current Image</label>
                <div style="border-radius:8px;overflow:hidden;height:140px;">
                    <img src="../images/products/<?= sanitize($product['image']) ?>" id="currentImg" style="width:100%;height:100%;object-fit:cover;" alt="">
                </div>
                <div style="font-size:11px;color:#aaa;margin-top:4px;"><?= sanitize($product['image']) ?></div>
            </div>

            <!-- Upload new -->
            <div class="mb-3">
                <label class="form-label">Upload New Image</label>
                <div class="img-preview-box" onclick="document.getElementById('imgUpload').click()" id="imgPreviewBox">
                    <div class="placeholder">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span style="font-size:13px;">Click to change image</span>
                    </div>
                </div>
                <input type="file" name="image" id="imgUpload" accept="image/*" style="display:none;" onchange="previewImage(this)">
                <?php if (isset($errors['image'])): ?><div class="text-danger mt-1" style="font-size:12.5px;"><?= $errors['image'] ?></div><?php endif; ?>
            </div>

            <div style="display:flex;align-items:center;gap:8px;color:#aaa;font-size:12px;margin:10px 0;">
                <div style="flex:1;height:1px;background:#eee;"></div>OR select existing<div style="flex:1;height:1px;background:#eee;"></div>
            </div>

            <div>
                <label class="form-label">Choose Existing Image</label>
                <select name="existing_image" class="form-select" id="existingImgSelect" onchange="previewExisting(this.value)">
                    <option value="">-- Keep current --</option>
                    <?php foreach ($existingImages as $img):
                        $fname = basename($img); ?>
                    <option value="<?= $fname ?>" <?= $product['image']==$fname?'selected':'' ?>><?= $fname ?></option>
                    <?php endforeach; ?>
                </select>
                <div id="existingPreview" class="mt-2" style="display:none;">
                    <img id="existingPreviewImg" src="" style="width:100%;height:100px;object-fit:cover;border-radius:8px;">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-admin-primary w-100 py-3" style="font-size:15px;border-radius:10px;">
            <i class="fas fa-save me-2"></i>Save Changes
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
            document.getElementById('currentImg').src = e.target.result;
            const box = document.getElementById('imgPreviewBox');
            box.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
        document.getElementById('existingImgSelect').value = '';
        document.getElementById('existingPreview').style.display = 'none';
    }
}
function previewExisting(val) {
    if (val) {
        document.getElementById('currentImg').src = '../images/products/' + val;
        document.getElementById('existingPreview').style.display = 'block';
        document.getElementById('existingPreviewImg').src = '../images/products/' + val;
    }
}
</script>
<?php include 'includes/footer.php'; ?>