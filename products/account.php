<?php
session_start();
require_once '../functions/connect.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

include '../componants/head.php';
include '../componants/header.php';
include '../componants/messages.php'; // Add this line
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Account Information</h5>
                    <hr>
                    <form action="../functions/update_profile.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Change Password</h5>
                    <hr>
                    <form action="../functions/change_password.php" method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" 
                                   name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" 
                                   name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Change Password</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order History</h5>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $orders_stmt = $conn->prepare("
                                    SELECT o.id, o.created_at, o.status,
                                           p.product_name, p.image,
                                           oi.quantity, oi.price as item_price
                                    FROM orders o
                                    JOIN order_items oi ON o.id = oi.order_id
                                    JOIN products p ON oi.product_id = p.id
                                    WHERE o.user_id = ?
                                    ORDER BY o.created_at DESC
                                ");
                                $orders_stmt->execute([$_SESSION['user_id']]);
                                while ($order = $orders_stmt->fetch()):
                                    $statusClass = match($order['status']) {
                                        'completed' => 'success',
                                        'processing' => 'info',
                                        'cancelled' => 'danger',
                                        default => 'warning'
                                    };
                                ?>
                                <tr>
                                    <td>#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if($order['image']): ?>
                                                <?php if(filter_var($order['image'], FILTER_VALIDATE_URL)): ?>
                                                    <img src="<?php echo htmlspecialchars($order['image']); ?>" 
                                                         class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <img src="../assets/images/products/<?php echo htmlspecialchars($order['image']); ?>" 
                                                         class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <img src="../assets/images/no-image.jpg" 
                                                     class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php endif; ?>
                                            <span><?php echo htmlspecialchars($order['product_name']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td>$<?php echo number_format($order['item_price'], 2); ?></td>
                                    <td>
                                        <div><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                                        <small class="text-muted"><?php echo date('h:i A', strtotime($order['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $statusClass; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

<script>
function viewOrderDetails(orderId) {
    window.location.href = `order_details.php?id=${orderId}`;
}
</script>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../componants/footer.php'; ?>