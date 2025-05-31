<?php
session_start();
require_once '../functions/connect.php';

// التحقق من صلاحيات المستخدم
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include '../componants/head.php';
?>

<div class="d-flex">
    <!-- تضمين الشريط الجانبي -->
    <?php include 'components/sidebar.php'; ?>

    <div class="flex-grow-1">
        <!-- تضمين الهيدر -->
        <?php include 'components/header.php'; ?>

        <!-- المحتوى الرئيسي -->
        <div class="p-4">
            <h2>المنتجات المحذوفة</h2>

            <!-- جدول المنتجات المحذوفة -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>السعر</th>
                            <th>تاريخ الحذف</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->query("SELECT * FROM products WHERE isDeleted = TRUE ORDER BY updated_at DESC");
                        while ($product = $stmt->fetch()) {
                        ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <?php if($product['image']): ?>
                                        <?php if(filter_var($product['image'], FILTER_VALIDATE_URL)): ?>
                                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <img src="../assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <img src="../assets/images/no-image.jpg" 
                                             alt="No Image Available"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $product['product_name']; ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['updated_at']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="restoreProduct(<?php echo $product['id']; ?>)">استعادة</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function restoreProduct(id) {
    if(confirm('هل أنت متأكد من استعادة هذا المنتج؟')) {
        window.location.href = '../functions/restore_product.php?id=' + id;
    }
}
</script>

<?php include '../componants/footer.php'; ?>