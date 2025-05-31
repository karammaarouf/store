<?php
session_start();
require_once '../functions/connect.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include '../componants/head.php';
?>

<div class="d-flex">
    <!-- Include Sidebar -->
    <?php include 'components/sidebar.php'; ?>

    <div class="flex-grow-1">
        <!-- Include Header -->
        <?php include 'components/header.php'; ?>

        <!-- Main Content -->
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Products Management</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    Add New Product
                </button>
            </div>

            <!-- Products Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->query("SELECT * FROM products WHERE isDeleted = FALSE ORDER BY created_at DESC");
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
                                <td><?php echo $product['created_at']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editProduct(<?php echo $product['id']; ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../functions/process_product.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="text" class="form-control mb-2" id="image_url" name="image_url" placeholder="Or enter image URL">
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Upload an image file or provide an image URL</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../functions/update_product.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="edit_product_name" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="edit_price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image" class="form-label">Product Image</label>
                        <input type="text" class="form-control mb-2" id="edit_image_url" name="image_url" placeholder="Or enter image URL">
                        <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                        <small class="text-muted">Upload a new image, enter URL, or leave empty to keep current image</small>
                        <div id="currentImagePreview" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editProduct(id) {
    // Fetch product details using AJAX
    fetch(`../functions/get_product.php?id=${id}`)
        .then(response => response.json())
        .then(product => {
            document.getElementById('edit_id').value = product.id;
            document.getElementById('edit_product_name').value = product.product_name;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_description').value = product.description;
            
            // Show current image
            const imagePreview = document.getElementById('currentImagePreview');
            if (product.image) {
                const isUrl = product.image.startsWith('http');
                const imageSrc = isUrl ? product.image : `../assets/images/products/${product.image}`;
                imagePreview.innerHTML = `<img src="${imageSrc}" alt="Current Image" style="max-width: 100px; margin-top: 10px;">`;
                if (isUrl) {
                    document.getElementById('edit_image_url').value = product.image;
                }
            } else {
                imagePreview.innerHTML = '';
            }
            
            // Show modal
            new bootstrap.Modal(document.getElementById('editProductModal')).show();
        })
        .catch(error => console.error('Error:', error));
}

function deleteProduct(id) {
    if(confirm('Are you sure you want to delete this product?')) {
        window.location.href = '../functions/delete_product.php?id=' + id;
    }
}
</script>
<?php include '../componants/footer.php'; ?>