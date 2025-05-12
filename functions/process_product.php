<?php
session_start();
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    
    // Handle image upload
    $image_name = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($file_ext, $allowed)) {
            $image_name = time() . '_' . $filename;
            $target_path = "../assets/images/products/" . $image_name;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                // Image uploaded successfully
            } else {
                $_SESSION['error'] = "Failed to upload image.";
                header("Location: ../dashboard/dashboard.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Allowed: jpg, jpeg, png, gif";
            header("Location: ../dashboard/dashboard.php");
            exit();
        }
    }

    try {
        $stmt = $conn->prepare("INSERT INTO products (product_name, price, description, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_name, $price, $description, $image_name]);

        $_SESSION['success'] = "Product added successfully!";
        header("Location: ../dashboard/dashboard.php");
        exit();

    } catch(PDOException $e) {
        $_SESSION['error'] = "Failed to add product: " . $e->getMessage();
        header("Location: ../dashboard/dashboard.php");
        exit();
    }
} else {
    header("Location: ../dashboard/dashboard.php");
    exit();
}
?>