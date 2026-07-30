<?php
require_once __DIR__ . '/includes/admin_header.php';

$msg = '';

// Update Order Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = sanitize($_POST['order_status']);

    if ($GLOBALS['db_connected']) {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            $msg = "Order status updated to <strong>$new_status</strong>.";
        } catch (Exception $e) {
            $msg = "Error updating order status.";
        }
    }
}

// Fetch orders
$orders = [];
if ($GLOBALS['db_connected']) {
    try {
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
        $orders = $stmt->fetchAll();
    } catch (Exception $e) {
        $orders = [];
    }
}
?>

<div style="margin-bottom: 24px;">
    <h3 style="font-size: 1.4rem; color: var(--primary-color);">Customer Orders Management</h3>
    <p style="font-size: 0.9rem; color: var(--text-muted);">View customer order details and update shipping status</p>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<div style="background: #fff; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); overflow: hidden; border: 1px solid var(--border-color);">
    <?php if (count($orders) > 0): ?>
        <table class="cart-table" style="box-shadow: none; margin-bottom: 0;">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Customer Name & Contact</th>
                    <th>Shipping Address</th>
                    <th>Payment</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--primary-color);"><?= sanitize($o['order_number']) ?></td>
                        <td style="font-size: 0.82rem; color: var(--text-muted);"><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></td>
                        <td>
                            <strong><?= sanitize($o['full_name']) ?></strong><br>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><?= sanitize($o['phone']) ?> | <?= sanitize($o['email']) ?></span>
                        </td>
                        <td style="font-size: 0.85rem; max-width: 200px;"><?= sanitize($o['address']) ?></td>
                        <td><span style="font-size: 0.85rem;"><?= sanitize($o['payment_method']) ?></span></td>
                        <td style="font-weight: 700; color: var(--primary-color);"><?= format_price($o['total_amount']) ?></td>
                        <td>
                            <span class="badge" style="background: #e8f5e9; color: #2e7d32;">
                                <?= sanitize($o['order_status']) ?>
                            </span>
                        </td>
                        <td>
                            <form action="orders.php" method="POST" style="display: flex; gap: 6px;">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <input type="hidden" name="update_status" value="1">
                                <select name="order_status" class="form-control" style="padding: 4px 8px; font-size: 0.8rem;" onchange="this.form.submit()">
                                    <option value="Processing" <?= $o['order_status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="Shipped" <?= $o['order_status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="Delivered" <?= $o['order_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="Cancelled" <?= $o['order_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 50px 0; color: var(--text-muted);">
            <i class="fas fa-box-open" style="font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 12px;"></i>
            <p>No customer orders placed in the database yet.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
