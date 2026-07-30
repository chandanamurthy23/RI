<?php
require_once __DIR__ . '/includes/admin_header.php';

$message = '';
$error = '';

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {
    $name = sanitize($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 1);
    $price = (float)($_POST['price'] ?? 0);
    $weight = sanitize($_POST['weight'] ?? '250g');
    $badge = sanitize($_POST['badge'] ?? 'Organic');
    $stock = (int)($_POST['stock'] ?? 100);
    $short_desc = sanitize($_POST['short_description'] ?? '');
    $desc = sanitize($_POST['description'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    $image_path = 'assets/images/products/multigrain-health-mix.png'; // default fallback

    // Image Upload Handling
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['product_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['product_image']['name']);
        $upload_dir = __DIR__ . '/../assets/images/products/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($file_tmp, $target_file)) {
            $image_path = 'assets/images/products/' . $file_name;
        }
    }

    if (!empty($name) && $price > 0) {
        if ($GLOBALS['db_connected']) {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, short_description, description, price, weight, badge, stock, image, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$category_id, $name, $slug, $short_desc, $desc, $price, $weight, $badge, $stock, $image_path]);
                $message = "Product <strong>$name</strong> added successfully!";
            } catch (Exception $e) {
                $error = "Database error adding product.";
            }
        } else {
            $message = "Product added (Preview Mode).";
        }
    } else {
        $error = "Product Name and valid Price are required.";
    }
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    if ($GLOBALS['db_connected']) {
        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$delete_id]);
            $message = "Product deleted successfully.";
        } catch (Exception $e) {
            $error = "Error deleting product.";
        }
    }
}

// Fetch all products
$products = [];
if ($GLOBALS['db_connected']) {
    try {
        $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
        $products = $stmt->fetchAll();
    } catch (Exception $e) {
        $products = [];
    }
}
if (empty($products)) {
    $products = get_mock_products();
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h3 style="font-size: 1.4rem; color: var(--primary-color);">Manage Store Products</h3>
    <button onclick="document.getElementById('addProductModal').style.display='block'" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </button>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<!-- Products Data Table -->
<div style="background: #fff; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); overflow: hidden; border: 1px solid var(--border-color);">
    <table class="cart-table" style="box-shadow: none; margin-bottom: 0;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Weight</th>
                <th>Badge</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td>#<?= $p['id'] ?></td>
                    <td>
                        <img src="../<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                    </td>
                    <td style="font-weight: 700; color: var(--primary-color);"><?= sanitize($p['name']) ?></td>
                    <td><span style="font-size: 0.85rem; color: var(--secondary-color); font-weight: 600;"><?= sanitize($p['category_name']) ?></span></td>
                    <td style="font-weight: 700;"><?= format_price($p['price']) ?></td>
                    <td><?= sanitize($p['weight']) ?></td>
                    <td><span class="badge badge-organic"><?= sanitize($p['badge']) ?></span></td>
                    <td><span style="color: green; font-weight: 600;"><?= $p['stock'] ?> units</span></td>
                    <td>
                        <a href="products.php?delete=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?');" style="color: var(--accent-color); font-size: 1rem;" title="Delete Product">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Product Modal Form -->
<div id="addProductModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 2000; overflow-y: auto; padding: 40px 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 1.3rem;">Add New RM Sampoorna Product</h3>
            <span onclick="document.getElementById('addProductModal').style.display='none'" style="font-size: 1.5rem; cursor: pointer;">&times;</span>
        </div>

        <form action="products.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_product">

            <div class="form-group">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Traditional Mysuru Sambar Powder">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-control">
                        <option value="1">Health & Multigrain Mixes</option>
                        <option value="2">Masala & Spice Powders</option>
                        <option value="3">Baby & Infant Food</option>
                        <option value="4">Traditional Sweets & Laddus</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Price (₹) *</label>
                    <input type="number" step="0.01" name="price" class="form-control" required placeholder="249.00">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Net Weight</label>
                    <input type="text" name="weight" class="form-control" placeholder="250g / 500g" value="250g">
                </div>
                <div class="form-group">
                    <label class="form-label">Badge Tag</label>
                    <input type="text" name="badge" class="form-control" placeholder="Organic / Pure" value="Organic">
                </div>
                <div class="form-group">
                    <label class="form-label">Initial Stock</label>
                    <input type="number" name="stock" class="form-control" value="100">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Product Image (Pouch Photo)</label>
                <input type="file" name="product_image" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label class="form-label">Short Summary</label>
                <input type="text" name="short_description" class="form-control" placeholder="100% natural homemade spice mix">
            </div>

            <div class="form-group">
                <label class="form-label">Full Description</label>
                <textarea name="description" rows="4" class="form-control" placeholder="Detailed ingredients & health benefits..."></textarea>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" onclick="document.getElementById('addProductModal').style.display='none'" class="btn" style="background: #e9ecef;">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Product</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
