<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'] ?? 0;
$action = $data['action'] ?? '';
$user_id = $_SESSION['user_id'];

try {
    $conn->beginTransaction();

    // Get the pending order ID
    $stmt = $conn->prepare("SELECT id FROM orders WHERE user_id = ? AND status = 'pending'");
    $stmt->execute([$user_id]);
    $order = $stmt->fetch();

    if (!$order) {
        throw new Exception('No pending order found');
    }

    $order_id = $order['id'];

    if ($action === 'remove') {
        // Remove the item from order_items
        $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ? AND product_id = ?");
        $stmt->execute([$order_id, $product_id]);

        // Check if there are any items left in the order
        $stmt = $conn->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $items_count = $stmt->fetchColumn();

        if ($items_count === 0) {
            // If no items left, delete the entire order
            $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$order_id]);
        } else {
            // Update order total
            $stmt = $conn->prepare("UPDATE orders SET total = (
                SELECT SUM(quantity * price) FROM order_items WHERE order_id = ?
            ) WHERE id = ?");
            $stmt->execute([$order_id, $order_id]);
        }
    }

    $conn->commit();
    echo json_encode(['success' => true]);

} catch(Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>