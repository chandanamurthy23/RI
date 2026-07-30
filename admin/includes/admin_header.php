<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Check Admin Authentication
$admin_page = basename($_SERVER['PHP_SELF']);
if ($admin_page !== 'login.php') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RM Sampoorna - Admin Control Panel</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: #112a1f; color: #d8f3dc; padding: 24px 0; flex-shrink: 0; }
        .admin-brand { padding: 0 24px 24px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 12px; }
        .admin-brand img { height: 42px; background: #fff; padding: 4px 8px; border-radius: 8px; }
        .admin-nav { list-style: none; padding: 20px 0; }
        .admin-nav li a { display: flex; align-items: center; gap: 12px; padding: 14px 24px; color: #b7e4c7; font-weight: 600; font-size: 0.95rem; transition: all 0.3s; }
        .admin-nav li a:hover, .admin-nav li a.active { background: rgba(212, 163, 115, 0.2); color: #fff; border-left: 4px solid var(--secondary-color); }
        .admin-content { flex-grow: 1; padding: 30px; background: #fdfbf7; overflow-x: auto; }
        .admin-header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: #fff; padding: 20px 30px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); }
        .stat-card { background: #fff; padding: 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px; }
        .stat-icon { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
    </style>
</head>
<body>

<?php if ($admin_page !== 'login.php'): ?>
<div class="admin-wrapper">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <img src="../assets/images/logo.png" alt="RM Sampoorna Logo">
            <div>
                <h4 style="color:#fff; font-size: 1rem;">Admin Panel</h4>
                <span style="font-size: 0.75rem; color: var(--secondary-color);">RM Sampoorna</span>
            </div>
        </div>

        <ul class="admin-nav">
            <li><a href="index.php" class="<?= $admin_page == 'index.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="products.php" class="<?= $admin_page == 'products.php' ? 'active' : '' ?>"><i class="fas fa-boxes-stacked"></i> Products</a></li>
            <li><a href="orders.php" class="<?= $admin_page == 'orders.php' ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Customer Orders</a></li>
            <li><a href="messages.php" class="<?= $admin_page == 'messages.php' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Contact Messages</a></li>
            <li style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px;">
                <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Live Site</a>
            </li>
            <li><a href="logout.php" style="color: #ff8a80;"><i class="fas fa-sign-out-alt"></i> Admin Logout</a></li>
        </ul>
    </aside>

    <!-- Main Admin Content Area -->
    <main class="admin-content">
        <div class="admin-header-bar">
            <div>
                <h2 style="font-size: 1.5rem; color: var(--primary-color);">Welcome, Admin Manager 👋</h2>
                <span style="font-size: 0.85rem; color: var(--text-muted);"><?= date('l, F j, Y') ?></span>
            </div>
            <div>
                <a href="../index.php" target="_blank" class="btn btn-outline" style="font-size: 0.85rem; padding: 8px 16px;"><i class="fas fa-globe"></i> Visit Store</a>
            </div>
        </div>
<?php endif; ?>
