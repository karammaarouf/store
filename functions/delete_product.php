<?php
session_start();
require_once 'connect.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        // تحديث حالة المنتج بدلاً من حذفه
        $stmt = $conn->prepare("CALL SoftDeleteProduct(:id)");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $_SESSION['success'] = "تم حذف المنتج بنجاح!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "فشل في حذف المنتج: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "معرف المنتج غير صالح";
}

header("Location: ../dashboard/dashboard.php");
exit();
?>