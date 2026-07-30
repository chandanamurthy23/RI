<?php
require_once __DIR__ . '/includes/admin_header.php';

$messages = [];
if ($GLOBALS['db_connected']) {
    try {
        $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC");
        $messages = $stmt->fetchAll();
    } catch (Exception $e) {
        $messages = [];
    }
}
?>

<div style="margin-bottom: 24px;">
    <h3 style="font-size: 1.4rem; color: var(--primary-color);">Customer Contact Inquiries</h3>
    <p style="font-size: 0.9rem; color: var(--text-muted);">View messages submitted through the website contact form</p>
</div>

<div style="background: #fff; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); overflow: hidden; border: 1px solid var(--border-color);">
    <?php if (count($messages) > 0): ?>
        <table class="cart-table" style="box-shadow: none; margin-bottom: 0;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email & Phone</th>
                    <th>Subject</th>
                    <th>Message Content</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $m): ?>
                    <tr>
                        <td style="font-size: 0.82rem; color: var(--text-muted);"><?= date('d M Y, h:i A', strtotime($m['created_at'])) ?></td>
                        <td style="font-weight: 700; color: var(--primary-color);"><?= sanitize($m['name']) ?></td>
                        <td style="font-size: 0.85rem;"><?= sanitize($m['email']) ?><br><span style="color: var(--text-muted);"><?= sanitize($m['phone'] ?? 'N/A') ?></span></td>
                        <td style="font-weight: 600;"><?= sanitize($m['subject'] ?? 'General Inquiry') ?></td>
                        <td style="font-size: 0.9rem; max-width: 320px; line-height: 1.5;"><?= sanitize($m['message']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 50px 0; color: var(--text-muted);">
            <i class="fas fa-inbox" style="font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 12px;"></i>
            <p>No customer messages submitted yet.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
