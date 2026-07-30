<?php
require_once __DIR__ . '/includes/header.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$product = null;

if ($GLOBALS['db_connected']) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
    } catch (Exception $e) {
        $product = null;
    }
}

if (!$product) {
    $all_mock = get_mock_products();
    foreach ($all_mock as $m) {
        if ($m['id'] == $product_id) {
            $product = $m;
            break;
        }
    }
    if (!$product) {
        $product = $all_mock[0];
    }
}

// Fetch related products
$related_products = [];
if ($GLOBALS['db_connected'] && $product) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? LIMIT 4");
        $stmt->execute([$product['category_id'], $product['id']]);
        $related_products = $stmt->fetchAll();
        
        // If not enough related products in the same category, fill with random ones
        if (count($related_products) < 4) {
            $limit = 4 - count($related_products);
            $exclude_ids = array_merge([$product['id']], array_column($related_products, 'id'));
            $placeholders = implode(',', array_fill(0, count($exclude_ids), '?'));
            $stmt2 = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id NOT IN ($placeholders) LIMIT $limit");
            $stmt2->execute($exclude_ids);
            $related_products = array_merge($related_products, $stmt2->fetchAll());
        }
    } catch (Exception $e) {
        $related_products = [];
    }
}

if (empty($related_products) && $product) {
    $all_mock = get_mock_products();
    foreach ($all_mock as $m) {
        if ($m['id'] != $product['id']) {
            $related_products[] = $m;
            if (count($related_products) >= 4) break;
        }
    }
}
?>

<div class="container" style="margin-top: 40px; margin-bottom: 60px;">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 24px; font-size: 0.9rem; color: var(--text-muted);">
        <a href="index.php">Home</a> / <a href="shop.php">Shop</a> / <span style="color: var(--primary-color); font-weight: 600;"><?= sanitize($product['name']) ?></span>
    </div>

    <!-- Product Detail Layout -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; background: #fff; padding: 40px; border-radius: var(--radius-lg); box-shadow: var(--shadow-md);">
        <!-- Product Image -->
        <div>
            <img src="<?= sanitize($product['image']) ?>" alt="<?= sanitize($product['name']) ?>" style="width: 100%; border-radius: var(--radius-md); max-height: 440px; object-fit: cover;">
        </div>

        <!-- Product Info & Actions -->
        <div>
            <span class="badge badge-organic" style="margin-bottom: 12px;"><?= sanitize($product['badge']) ?></span>
            <p style="font-size: 0.85rem; color: var(--secondary-color); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-top: 8px;">
                <?= sanitize($product['category_name']) ?>
            </p>
            <h1 style="font-size: 2.2rem; margin-bottom: 14px;"><?= sanitize($product['name']) ?></h1>
            
            <div style="display: flex; align-items: baseline; gap: 16px; margin-bottom: 20px;">
                <span style="font-size: 2.2rem; font-weight: 800; color: var(--primary-color); font-family: 'Outfit', sans-serif;">
                    <?= format_price($product['price']) ?>
                </span>
                <span style="color: var(--text-muted); font-size: 0.95rem;">(Inclusive of all taxes)</span>
            </div>

            <p style="color: var(--text-muted); font-size: 1rem; line-height: 1.7; margin-bottom: 24px;">
                <?= sanitize($product['description']) ?>
            </p>

            <div style="background: #fdfbf7; border-left: 4px solid var(--secondary-color); padding: 16px; border-radius: var(--radius-sm); margin-bottom: 24px;">
                <p style="font-size: 0.9rem; margin-bottom: 4px;"><strong>Net Weight:</strong> <?= sanitize($product['weight']) ?></p>
                <p style="font-size: 0.9rem; margin-bottom: 4px;"><strong>Availability:</strong> <span style="color: green; font-weight: 600;">In Stock (<?= $product['stock'] ?> units)</span></p>
                <p style="font-size: 0.9rem;"><strong>Formulation:</strong> 100% Natural, No Added Colors or Preservatives</p>
            </div>

            <!-- Add to Cart Form -->
            <form action="cart.php" method="POST" style="display: flex; gap: 16px; align-items: center; margin-bottom: 30px;">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                <div class="qty-control">
                    <button type="button" class="qty-btn" onclick="updateQty(this, -1)">-</button>
                    <input type="number" name="quantity" value="1" min="1" class="qty-input">
                    <button type="button" class="qty-btn" onclick="updateQty(this, 1)">+</button>
                </div>

                <button type="submit" class="btn btn-primary" style="flex-grow: 1;">
                    <i class="fas fa-shopping-basket"></i> Add to Cart
                </button>
            </form>

            <div style="border-top: 1px solid var(--border-color); padding-top: 20px; display: flex; gap: 30px;">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted);">
                    <i class="fas fa-shield-halved" style="color: var(--primary-color);"></i> 100% Quality Guaranteed
                </div>
                <div style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted);">
                    <i class="fas fa-leaf" style="color: var(--primary-color);"></i> Organic Farm Ingredients
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products Section -->
    <?php if (!empty($related_products)): ?>
    <div style="margin-top: 80px;">
        <h2 style="font-size: 1.8rem; margin-bottom: 25px; color: #1b4332;">Related Products</h2>
        <p style="color: var(--text-muted); margin-bottom: 30px; font-size: 0.95rem;">Fresh products you might also need</p>
        
        <div class="product-grid">
            <?php foreach ($related_products as $rel_product): ?>
                <div class="product-card">
                    <div class="product-img-wrapper">
                        <a href="product.php?id=<?= $rel_product['id'] ?>">
                            <img src="<?= sanitize($rel_product['image']) ?>" alt="<?= sanitize($rel_product['name']) ?>" class="product-img">
                        </a>
                        <div class="product-badge-wrap">
                            <span class="badge-circle">
                                <?= sanitize($rel_product['badge']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="product-details">
                        <div class="product-header">
                            <h3 class="product-title">
                                <a href="product.php?id=<?= $rel_product['id'] ?>"><?= sanitize($rel_product['name']) ?></a>
                            </h3>
                            <span class="product-price"><?= format_price($rel_product['price']) ?></span>
                        </div>
                        
                        <span class="product-brand">RM Sampoorna</span>
                        
                        <p class="product-short-desc">
                            <?= sanitize($rel_product['short_description'] ?? '') ?>
                        </p>
                        
                        <form action="cart.php" method="POST" class="add-cart-form">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $rel_product['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="add-cart-btn-full">
                                ADD TO CART
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
