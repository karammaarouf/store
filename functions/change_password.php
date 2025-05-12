<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];

    try {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['message'] = "Current password is incorrect";
            $_SESSION['message_type'] = "danger";
            header("Location: ../products/account.php");
            exit();
        }

        // Validate new password
        if ($new_password !== $confirm_password) {
            $_SESSION['message'] = "New passwords do not match";
            $_SESSION['message_type'] = "danger";
            header("Location: ../products/account.php");
            exit();
        }

        if (strlen($new_password) < 8) {
            $_SESSION['message'] = "Password must be at least 8 characters long";
            $_SESSION['message_type'] = "danger";
            header("Location: ../products/account.php");
            exit();
        }

        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);

        $_SESSION['message'] = "Password changed successfully";
        $_SESSION['message_type'] = "success";

    } catch (PDOException $e) {
        $_SESSION['message'] = "Error changing password";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: ../products/account.php");
    exit();
} else {
    header("Location: ../products/account.php");
    exit();
}
?>