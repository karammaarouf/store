<?php
session_start();
if (!isset($_SESSION['logged_in']) && isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_token'])) {
    require_once 'functions/connect.php';
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND remember_token = ? AND token_expiry > NOW()");
    $stmt->execute([$_COOKIE['remember_user'], $_COOKIE['remember_token']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
    }
}
?>
<?php
include 'componants/head.php';
include 'componants/header.php';
?>

<!-- Main Content Area -->
<div class="container mt-4">
    <h1>Welcome to Our Store</h1>
    <!-- Add your main content here -->
</div>

<?php
include 'componants/footer.php';
?>