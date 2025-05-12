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
        // Get product image before deletion
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        // Delete product from database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        // Delete product image if exists
        if ($product && $product['image']) {
            $image_path = "../assets/images/products/" . $product['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $_SESSION['success'] = "Product deleted successfully!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Failed to delete product: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid product ID";
}

header("Location: ../dashboard/dashboard.php");
exit();
?>