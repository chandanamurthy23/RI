<?php
require_once __DIR__ . '/includes/header.php';

$order = $_SESSION['last_order'] ?? null;
$order_number = isset($_GET['order']) ? sanitize($_GET['order']) : ($order['order_number'] ?? 'RM-ORDER');
?>

<div class="container" style="margin-top: 60px; margin-bottom: 80px;">
    <div style="max-width: 680px; margin: 0 auto; background: #fff; padding: 40px; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); text-align: center;">
        <div style="width: 70px; height: 70px; background: #e8f5e9; color: #2e7d32; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 20px;">
            <i class="fas fa-check"></i>
        </div>

        <h1 style="font-size: 2rem; color: var(--primary-color); margin-bottom: 8px;">Order Placed Successfully!</h1>
        <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 24px;">
            Thank you for shopping with RM's Sampoorna. Your order reference number is: <strong style="color: var(--secondary-color);"><?= $order_number ?></strong>
        </p>

        <?php if ($order): ?>
            <div style="background: #fdfbf7; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; text-align: left; margin-bottom: 30px;">
                <h3 style="font-size: 1.1rem; margin-bottom: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Order Details</h3>
                <p style="font-size: 0.9rem; margin-bottom: 6px;"><strong>Recipient:</strong> <?= sanitize($order['full_name']) ?></p>
                <p style="font-size: 0.9rem; margin-bottom: 6px;"><strong>Phone:</strong> <?= sanitize($order['phone']) ?></p>
                <p style="font-size: 0.9rem; margin-bottom: 6px;"><strong>Shipping Address:</strong> <?= sanitize($order['address']) ?></p>
                <p style="font-size: 0.9rem; margin-bottom: 6px;"><strong>Payment Method:</strong> <?= sanitize($order['payment_method']) ?></p>
                <p style="font-size: 0.9rem; font-weight: 700; color: var(--primary-color); margin-top: 10px;">Total Paid / Due: <?= format_price($order['total_amount']) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($order && $order['payment_method'] === 'UPI / Online Payment'): ?>
            <div style="background: #fffcf2; border: 1px dashed #f5b041; border-radius: var(--radius-md); padding: 24px; text-align: center; margin-bottom: 30px;">
                <h3 style="color: #d68910; margin-bottom: 12px; font-size: 1.2rem;"><i class="fas fa-qrcode"></i> Complete Your Payment</h3>
                <p style="font-size: 0.95rem; color: #555; margin-bottom: 16px;">
                    Scan the QR code using any UPI app (GPay, PhonePe, Paytm) to pay <strong><?= format_price($order['total_amount']) ?></strong>.
                </p>
                <?php
                    // Dynamic UPI String Generation
                    $upi_id = "merchant@upi"; // Replace with actual business UPI ID
                    $upi_name = "RM Sampoorna";
                    $amount = $order['total_amount'];
                    $order_note = "Order " . $order_number;
                    $upi_string = "upi://pay?pa={$upi_id}&pn=" . urlencode($upi_name) . "&am={$amount}&cu=INR&tn=" . urlencode($order_note);
                    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode($upi_string);
                ?>
                <img src="<?= $qr_url ?>" alt="UPI QR Code" style="width: 200px; height: 200px; border: 10px solid #fff; box-shadow: var(--shadow-sm); border-radius: 12px; margin-bottom: 15px;">
                <p style="font-size: 0.85rem; color: var(--text-muted);">
                    Once paid, we will verify the transaction and process your order immediately.<br>
                    <strong>UPI ID:</strong> <?= $upi_id ?>
                </p>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 16px; justify-content: center;">
            <a href="shop.php" class="btn btn-primary"><i class="fas fa-store"></i> Continue Shopping</a>
            <a href="index.php" class="btn btn-outline">Back to Home</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
