<?php
session_start();
require_once 'connect.php';

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        header('Content-Type: application/json');
        echo json_encode($product);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch product']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID not provided']);
}
?>