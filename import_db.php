<?php
// Database credentials
$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    // 1. Connect without database to create it
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<h3>Connected to MySQL!</h3>";
    
    // 2. Read the SQL file
    $sql_file = __DIR__ . '/database.sql';
    if (!file_exists($sql_file)) {
        die("<p style='color:red;'>Error: database.sql not found.</p>");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // 3. Execute the SQL file
    $pdo->exec($sql_content);
    
    echo "<h3 style='color:green;'>✅ Database imported successfully!</h3>";
    echo "<p>You can now log into your website's admin panel.</p>";
    echo "<p><a href='http://localhost/Rithamaya_website/admin/' style='padding: 10px 20px; background: blue; color: white; text-decoration: none; border-radius: 5px;'>Go to Admin Panel</a></p>";
    
} catch (PDOException $e) {
    echo "<h3 style='color:red;'>❌ Database Error</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>This means your MySQL root password is NOT blank, or MySQL is not running properly in XAMPP.</p>";
}
?>
