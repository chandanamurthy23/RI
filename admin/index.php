<?php
require_once __DIR__ . '/includes/admin_header.php';

// Fetch stats from DB
$total_products = 0;
$total_orders = 0;
$total_revenue = 0;
$total_messages = 0;
$recent_orders = [];

if ($GLOBALS['db_connected']) {
    try {
        $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE order_status != 'Cancelled'")->fetchColumn() ?: 0;
        $total_messages = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();

        $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
        $recent_orders = $stmt->fetchAll();
    } catch (Exception $e) {
        $recent_orders = [];
    }
} else {
    $all_mock = get_mock_products();
    $total_products = count($all_mock);
    $total_orders = 3;
    $total_revenue = 1450.00;
    $total_messages = 2;
}
?>

<!-- Admin Stats Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-icon" style="background: #e8f5e9; color: #2e7d32;"><i class="fas fa-indian-rupee-sign"></i></div>
        <div>
            <h3 style="font-size: 1.4rem; color: var(--primary-color);"><?= format_price($total_revenue) ?></h3>
            <span style="font-size: 0.82rem; color: var(--text-muted);">Total Store Revenue</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #e3f2fd; color: #1565c0;"><i class="fas fa-shopping-bag"></i></div>
        <div>
            <h3 style="font-size: 1.4rem; color: var(--primary-color);"><?= $total_orders ?></h3>
            <span style="font-size: 0.82rem; color: var(--text-muted);">Customer Orders</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #fff3e0; color: #ef6c00;"><i class="fas fa-box-open"></i></div>
        <div>
            <h3 style="font-size: 1.4rem; color: var(--primary-color);"><?= $total_products ?></h3>
            <span style="font-size: 0.82rem; color: var(--text-muted);">Total Active Products</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #f3e5f5; color: #7b1fa2;"><i class="fas fa-envelope-open-text"></i></div>
        <div>
            <h3 style="font-size: 1.4rem; color: var(--primary-color);"><?= $total_messages ?></h3>
            <span style="font-size: 0.82rem; color: var(--text-muted);">Customer Messages</span>
        </div>
    </div>
</div>

<!-- Recent Orders Section -->
<div style="background: #fff; padding: 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 1.2rem; color: var(--primary-color);">Recent Customer Orders</h3>
        <a href="orders.php" class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 14px;">View All Orders</a>
    </div>

    <?php if (count($recent_orders) > 0): ?>
        <table class="cart-table" style="box-shadow: none; margin-bottom: 0;">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--primary-color);"><?= sanitize($order['order_number']) ?></td>
                        <td><?= sanitize($order['full_name']) ?></td>
                        <td><?= sanitize($order['phone']) ?></td>
                        <td><?= sanitize($order['payment_method']) ?></td>
                        <td>
                            <span class="badge" style="background: #e8f5e9; color: #2e7d32;">
                                <?= sanitize($order['order_status']) ?>
                            </span>
                        </td>
                        <td style="font-weight: 700;"><?= format_price($order['total_amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 30px; color: var(--text-muted);">
            No customer orders placed yet.
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
