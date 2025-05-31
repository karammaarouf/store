<?php
require_once '../functions/connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../componants/head.php'; ?>
    <title>Product Logs - Admin Dashboard</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'components/sidebar.php'; ?>
            
            <div class="col-md-10 p-4">
                <h2>Product Logs</h2>
                <div class="table-responsive mt-4">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product ID</th>
                                <th>Action</th>
                                <th>Old Name</th>
                                <th>Old Price</th>
                                <th>Old Description</th>
                                <th>New Name</th>
                                <th>New Price</th>
                                <th>New Description</th>
                                <th>Changed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT ph.*, p.product_name 
                                     FROM product_history ph 
                                     LEFT JOIN products p ON ph.product_id = p.id 
                                     ORDER BY ph.changed_at DESC";
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $logs = $stmt->fetchAll();

                            foreach ($logs as $log) {
                                echo "<tr>";
                                echo "<td>{$log['id']}</td>";
                                echo "<td>{$log['product_id']}</td>";
                                echo "<td><span class='badge bg-" . 
                                    ($log['action'] == 'INSERT' ? 'success' : 
                                    ($log['action'] == 'UPDATE' ? 'warning' : 'danger')) . 
                                    "'>{$log['action']}</span></td>";
                                echo "<td>{$log['old_name']}</td>";
                                echo "<td>" . ($log['old_price'] ? '$' . number_format($log['old_price'], 2) : '') . "</td>";
                                echo "<td>{$log['old_description']}</td>";
                                echo "<td>{$log['new_name']}</td>";
                                echo "<td>" . ($log['new_price'] ? '$' . number_format($log['new_price'], 2) : '') . "</td>";
                                echo "<td>{$log['new_description']}</td>";
                                echo "<td>{$log['changed_at']}</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include '../componants/footer.php'; ?>
</body>
</html>