<?php
session_start();
require_once 'connect.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // Prevent deleting own account
    if ($user_id === $_SESSION['user_id']) {
        $_SESSION['message'] = "You cannot delete your own account";
        $_SESSION['message_type'] = "danger";
        header("Location: ../dashboard/users.php");
        exit();
    }

    try {
        // Begin transaction
        $conn->beginTransaction();

        // Delete related orders first (cascade will handle order_items)
        $stmt = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        $conn->commit();

        $_SESSION['message'] = "User deleted successfully";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['message'] = "Error deleting user";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "Invalid request";
    $_SESSION['message_type'] = "danger";
}

header("Location: ../dashboard/users.php");
exit();
?>