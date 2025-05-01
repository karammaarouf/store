<?php
session_start();
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    try {
        // Get user from database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;

            // Handle remember me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                // Set cookie for 7 days
                setcookie('remember_user', $user['id'], time() + (7 * 24 * 60 * 60), '/');
                setcookie('remember_token', $token, time() + (7 * 24 * 60 * 60), '/');
                
                // Update user's remember token in database
                $stmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expiry = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE id = ?");
                $stmt->execute([$token, $user['id']]);
            }

            $_SESSION['success'] = "Welcome back, " . $user['username'] . "!";
            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password";
            header("Location: ../auth/login.php");
            exit();
        }

    } catch(PDOException $e) {
        $_SESSION['error'] = "Login failed. Please try again.";
        header("Location: ../auth/login.php");
        exit();
    }
} else {
    header("Location: ../auth/login.php");
    exit();
}
?>