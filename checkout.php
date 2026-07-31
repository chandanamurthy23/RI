<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

$cart_items = $_SESSION['cart'] ?? [];
if (empty($cart_items)) {
    header("Location: shop.php");
    exit;
}

$subtotal = get_cart_total();
$shipping = $subtotal > 499 ? 0.00 : 50.00;
$grand_total = $subtotal + $shipping;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $payment_method = sanitize($_POST['payment_method'] ?? 'Cash on Delivery');

    if (empty($full_name)) $errors[] = "Full Name is required.";
    if (empty($email)) $errors[] = "Email address is required.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($address)) $errors[] = "Delivery address is required.";

    if (empty($errors)) {
        $order_number = 'RM-' . strtoupper(uniqid());
        $user_id = $_SESSION['user_id'] ?? null;

        if ($GLOBALS['db_connected']) {
            try {
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, full_name, email, phone, address, total_amount, payment_method, payment_status, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 'Processing')");
                $stmt->execute([$user_id, $order_number, $full_name, $email, $phone, $address, $grand_total, $payment_method]);
                $order_id = $pdo->lastInsertId();

                $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
                foreach ($cart_items as $item) {
                    $item_stmt->execute([$order_id, $item['id'], $item['name'], $item['quantity'], $item['price']]);
                }
            } catch (Exception $e) {
                // Fallback handle
                $order_id = time();
            }
        } else {
            $order_id = time();
        }

        // Save order details to session for confirmation page & clear cart
        $_SESSION['last_order'] = [
            'order_number' => $order_number,
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'total_amount' => $grand_total,
            'payment_method' => $payment_method,
            'items' => $cart_items
        ];

        $_SESSION['cart'] = [];
        header("Location: order_success.php?order=" . urlencode($order_number));
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="container">
        <h1>Checkout & Delivery</h1>
        <p>Enter your delivery details to complete your order</p>
    </div>
</div>

<div class="container" style="margin-bottom: 70px;">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= $err ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="checkout.php" method="POST" style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
        <!-- Customer Delivery Form -->
        <div style="background: #fff; padding: 30px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <h3 style="margin-bottom: 20px; font-size: 1.3rem;">1. Shipping Information</h3>

            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="full_name" class="form-control" required placeholder="e.g. Ramesh Kumar" value="<?= sanitize($_SESSION['user_name'] ?? '') ?>">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" required placeholder="you@example.com" value="<?= sanitize($_SESSION['user_email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input type="tel" name="phone" class="form-control" required placeholder="+91 98765 43210">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Complete Shipping Address *</label>
                <textarea name="address" rows="4" class="form-control" required placeholder="House No, Street, Landmark, City, State, Pincode"></textarea>
            </div>

            <h3 style="margin: 30px 0 20px; font-size: 1.3rem;">2. Payment Method</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <label style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer; background: #fdfbf7;">
                    <input type="radio" name="payment_method" value="Cash on Delivery" checked>
                    <div>
                        <strong>Cash on Delivery (COD)</strong>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Pay in cash upon doorstep delivery</p>
                    </div>
                </label>

                <label style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer;">
                    <input type="radio" name="payment_method" value="UPI / Online Payment">
                    <div>
                        <strong>UPI / GPay / PhonePe / Paytm</strong>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Instant QR code or UPI payment</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Order Summary Box -->
        <div>
            <div class="cart-summary-card">
                <h3 style="margin-bottom: 20px; font-size: 1.3rem;">Order Overview</h3>

                <div style="max-height: 240px; overflow-y: auto; margin-bottom: 20px;">
                    <?php foreach ($cart_items as $item): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed var(--border-color);">
                            <div>
                                <h4 style="font-size: 0.88rem;"><?= sanitize($item['name']) ?></h4>
                                <span style="font-size: 0.78rem; color: var(--text-muted);">Qty: <?= $item['quantity'] ?> × <?= format_price($item['price']) ?></span>
                            </div>
                            <span style="font-weight: 600; font-size: 0.9rem;"><?= format_price((float)$item['price'] * (int)$item['quantity']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span><?= format_price($subtotal) ?></span>
                </div>

                <div class="summary-row">
                    <span>Shipping</span>
                    <span><?= $shipping == 0 ? '<span style="color:green; font-weight:600;">FREE</span>' : format_price($shipping) ?></span>
                </div>

                <div class="summary-row total">
                    <span>Amount Payable</span>
                    <span><?= format_price($grand_total) ?></span>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 24px;">
                    <i class="fas fa-lock"></i> Place Order Now
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
