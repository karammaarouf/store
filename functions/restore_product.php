<?php
session_start();
require_once 'connect.php';

// التحقق من صلاحيات المستخدم
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        // تحديث حالة المنتج لاستعادته
        $stmt = $conn->prepare("UPDATE products SET isDeleted = FALSE WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = "تمت استعادة المنتج بنجاح!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "فشل في استعادة المنتج: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "معرف المنتج غير صالح";
}

header("Location: ../dashboard/deleted_products.php");
exit();
?>