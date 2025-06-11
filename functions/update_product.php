<?php
session_start();
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $product_name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);

    try {
        // Get current product image
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $current_product = $stmt->fetch();
        
        $image_name = $current_product['image'];

        // Handle new image upload if provided
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if(in_array($file_ext, $allowed)) {
                // Delete old image if exists
                if($current_product['image'] && file_exists("../assets/images/products/" . $current_product['image'])) {
                    unlink("../assets/images/products/" . $current_product['image']);
                }
                
                $image_name = time() . '_' . $filename;
                $target_path = "../assets/images/products/" . $image_name;
                
                if(!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    throw new Exception("Failed to upload new image");
                }
            } else {
                throw new Exception("Invalid file type. Allowed: jpg, jpeg, png, gif");
            }
        }

        // Update product in database
        $stmt = $conn->prepare("CALL UpdateProduct(:id, :name, :price, :description, :image)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $product_name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image_name);
        $stmt->execute();

        $_SESSION['success'] = "Product updated successfully!";
        header("Location: ../dashboard/dashboard.php");
        exit();

    } catch(Exception $e) {
        $_SESSION['error'] = "Failed to update product: " . $e->getMessage();
        header("Location: ../dashboard/edit_product.php?id=" . $id);
        exit();
    }
} else {
    header("Location: ../dashboard/dashboard.php");
    exit();
}
?>