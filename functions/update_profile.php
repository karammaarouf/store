<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $user_id = $_SESSION['user_id'];

    try {
        // Check if email already exists for another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $_SESSION['message'] = "Email already exists";
            $_SESSION['message_type'] = "danger";
            header("Location: ../products/account.php");
            exit();
        }

        // Update user information
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $user_id]);

        // Update session username
        $_SESSION['username'] = $username;

        $_SESSION['message'] = "Profile updated successfully";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error updating profile";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: ../products/account.php");
    exit();
} else {
    header("Location: ../products/account.php");
    exit();
}
?>