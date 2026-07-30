<?php
require_once __DIR__ . '/includes/admin_header.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        if ($GLOBALS['db_connected']) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_name'] = $user['full_name'];
                    $_SESSION['admin_email'] = $user['email'];
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Invalid admin credentials or unauthorized account.";
                }
            } catch (Exception $e) {
                $error = "Database query error.";
            }
        } else {
            // Fallback preview login
            if ($email === 'admin@rithamaya.com' && $password === 'admin123') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_name'] = 'Admin Manager';
                $_SESSION['admin_email'] = 'admin@rithamaya.com';
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid credentials. Try: admin@rithamaya.com / admin123";
            }
        }
    } else {
        $error = "Please enter admin email and password.";
    }
}
?>

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #112a1f, #1b4332); padding: 20px;">
    <div style="max-width: 420px; width: 100%; background: #fff; padding: 40px; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); text-align: center;">
        <img src="../assets/images/logo.png" alt="RM Sampoorna Logo" style="height: 60px; margin-bottom: 16px;">
        <h2 style="font-size: 1.6rem; color: var(--primary-color); margin-bottom: 6px;">Admin Panel Login</h2>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 26px;">Authorized staff portal for RM's Sampoorna</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" style="font-size: 0.88rem;"><?= $error ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" style="text-align: left;">
            <div class="form-group">
                <label class="form-label">Admin Email *</label>
                <input type="email" name="email" class="form-control" required placeholder="admin@rithamaya.com" value="admin@rithamaya.com">
            </div>

            <div class="form-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••" value="admin123">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                <i class="fas fa-lock"></i> Login To Control Panel
            </button>
        </form>

        <div style="margin-top: 24px; font-size: 0.8rem; color: var(--text-muted); border-top: 1px solid var(--border-color); padding-top: 16px;">
            Default Credentials: <strong>admin@rithamaya.com</strong> / <strong>admin123</strong>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
