<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'] ?? 0;
$quantity = $data['quantity'] ?? 1;
$user_id = $_SESSION['user_id'];

try {
    $conn->beginTransaction();

    // Check if product exists and get price
    $stmt = $conn->prepare("SELECT id, price FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        throw new Exception('Product not found');
    }

    // Check if user has a pending order
    $stmt = $conn->prepare("SELECT id FROM orders WHERE user_id = ? AND status = 'pending'");
    $stmt->execute([$user_id]);
    $pending_order = $stmt->fetch();

    if (!$pending_order) {
        // Create new order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, 0, 'pending')");
        $stmt->execute([$user_id]);
        $order_id = $conn->lastInsertId();
    } else {
        $order_id = $pending_order['id'];
    }

    // Check if product already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM order_items WHERE order_id = ? AND product_id = ?");
    $stmt->execute([$order_id, $product_id]);
    $existing_item = $stmt->fetch();

    if ($existing_item) {
        // Update quantity
        $stmt = $conn->prepare("UPDATE order_items SET quantity = quantity + ? WHERE id = ?");
        $stmt->execute([$quantity, $existing_item['id']]);
    } else {
        // Add new item
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $product_id, $quantity, $product['price']]);
    }

    // Update order total
    $stmt = $conn->prepare("UPDATE orders SET total = (
        SELECT SUM(quantity * price) FROM order_items WHERE order_id = ?
    ) WHERE id = ?");
    $stmt->execute([$order_id, $order_id]);

    $conn->commit();
    echo json_encode(['success' => true]);

} catch(Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>