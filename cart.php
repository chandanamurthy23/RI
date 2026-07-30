<?php
require_once __DIR__ . '/includes/header.php';

// Handle Cart Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? sanitize($_POST['action']) : '';
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Fetch product details
    $target_product = null;
    if ($GLOBALS['db_connected']) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $target_product = $stmt->fetch();
        } catch (Exception $e) {
            $target_product = null;
        }
    }

    if (!$target_product) {
        $all_mock = get_mock_products();
        foreach ($all_mock as $m) {
            if ($m['id'] == $product_id) {
                $target_product = $m;
                break;
            }
        }
    }

    if ($action === 'add' && $target_product) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $target_product['id'],
                'name' => $target_product['name'],
                'price' => $target_product['price'],
                'weight' => $target_product['weight'],
                'image' => $target_product['image'],
                'quantity' => $quantity
            ];
        }
        $_SESSION['success_msg'] = "Added <strong>" . sanitize($target_product['name']) . "</strong> to your cart.";
    } elseif ($action === 'update') {
        if (isset($_SESSION['cart'][$product_id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                $_SESSION['success_msg'] = "Cart updated successfully.";
            } else {
                unset($_SESSION['cart'][$product_id]);
                $_SESSION['success_msg'] = "Item removed from cart.";
            }
        }
    } elseif ($action === 'remove') {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['success_msg'] = "Item removed from cart.";
        }
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        $_SESSION['success_msg'] = "Cart cleared.";
    }

    header("Location: cart.php");
    exit;
}

$cart_items = $_SESSION['cart'] ?? [];
$subtotal = get_cart_total();
$shipping = $subtotal > 499 || $subtotal == 0 ? 0.00 : 50.00;
$grand_total = $subtotal + $shipping;
?>

<div class="page-banner">
    <div class="container">
        <h1>Your Shopping Cart</h1>
        <p>Review your selected organic products before proceeding to checkout</p>
    </div>
</div>

<div class="container" style="margin-bottom: 70px;">
    <?php if (count($cart_items) > 0): ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
            <!-- Cart Table -->
            <div>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): 
                            $item_total = (float)$item['price'] * (int)$item['quantity'];
                        ?>
                            <tr>
                                <td>
                                    <div class="cart-product-flex">
                                        <img src="<?= sanitize($item['image']) ?>" alt="<?= sanitize($item['name']) ?>" class="cart-thumb">
                                        <div>
                                            <h4 style="font-size: 0.95rem;"><?= sanitize($item['name']) ?></h4>
                                            <span style="font-size: 0.8rem; color: var(--text-muted);"><?= sanitize($item['weight']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-weight: 600; color: var(--primary-color);">
                                    <?= format_price($item['price']) ?>
                                </td>
                                <td>
                                    <form action="cart.php" method="POST" style="display: inline-flex;">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <div class="qty-control">
                                            <button type="button" class="qty-btn" onclick="updateQty(this, -1); this.form.submit();">-</button>
                                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="qty-input" onchange="this.form.submit()">
                                            <button type="button" class="qty-btn" onclick="updateQty(this, 1); this.form.submit();">+</button>
                                        </div>
                                    </form>
                                </td>
                                <td style="font-weight: 700; color: var(--primary-color);">
                                    <?= format_price($item_total) ?>
                                </td>
                                <td>
                                    <form action="cart.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <button type="submit" style="background: none; border: none; color: var(--accent-color); cursor: pointer; font-size: 1.1rem;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between;">
                    <a href="shop.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn" style="background: #e9ecef; color: var(--text-main);">Clear Cart</button>
                    </form>
                </div>
            </div>

            <!-- Cart Summary Card -->
            <div>
                <div class="cart-summary-card">
                    <h3 style="margin-bottom: 20px; font-size: 1.3rem;">Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Items Subtotal</span>
                        <span><?= format_price($subtotal) ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Shipping Fee</span>
                        <span><?= $shipping == 0 ? '<span style="color:green; font-weight:600;">FREE</span>' : format_price($shipping) ?></span>
                    </div>

                    <?php if ($shipping > 0): ?>
                        <p style="font-size: 0.78rem; color: var(--secondary-color); margin-top: 4px;">
                            💡 Add <?= format_price(500 - $subtotal) ?> more for FREE shipping!
                        </p>
                    <?php endif; ?>

                    <div class="summary-row total">
                        <span>Grand Total</span>
                        <span><?= format_price($grand_total) ?></span>
                    </div>

                    <a href="checkout.php" class="btn btn-primary" style="width: 100%; margin-top: 24px; text-align: center;">
                        Proceed to Checkout <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 70px 0; background: #fff; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <i class="fas fa-shopping-basket" style="font-size: 3.5rem; color: var(--secondary-color); margin-bottom: 20px;"></i>
            <h2>Your Shopping Cart is Empty</h2>
            <p style="color: var(--text-muted); margin-bottom: 26px;">Looks like you haven't added any delicious masalas or health mixes yet!</p>
            <a href="shop.php" class="btn btn-primary">Start Shopping Now</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
