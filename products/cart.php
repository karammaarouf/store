<?php
session_start();
require_once '../functions/connect.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../componants/head.php';
include '../componants/header.php';
include '../componants/messages.php'; // Add this line

// Get cart items from database
$cart_items = [];
$total = 0;

try {
    // Get pending order for current user
    $stmt = $conn->prepare("
        SELECT o.id as order_id, o.total,
               oi.quantity, oi.price as item_price,
               p.id, p.product_name, p.image, p.description
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ? AND o.status = 'pending'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
    
    if (!empty($cart_items)) {
        $total = $cart_items[0]['total'];
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="container my-5">
    <h2 class="mb-4">Shopping Cart</h2>

    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="../index.php">Continue shopping</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="../assets/images/products/<?php echo $item['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                         style="width: 50px; height: 50px; object-fit: cover;"
                                         class="me-3">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item['item_price'], 2); ?></td>
                            <td>
                                <div class="input-group" style="width: 130px;">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="updateQuantity(<?php echo $item['id']; ?>, 'decrease')">-</button>
                                    <input type="text" class="form-control text-center" 
                                           value="<?php echo $item['quantity']; ?>" readonly>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="updateQuantity(<?php echo $item['id']; ?>, 'increase')">+</button>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item['item_price'] * $item['quantity'], 2); ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm" 
                                        onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="../index.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Continue Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQuantity(productId, action) {
    fetch('../functions/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            action: action
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function removeFromCart(productId) {
    if (confirm('Are you sure you want to remove this item?')) {
        fetch('../functions/update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                action: 'remove'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

</script>

<?php include '../componants/footer.php'; ?>