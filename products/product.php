<?php
session_start();
require_once '../functions/connect.php';

// Get product ID and fetch details
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        header("Location: ../index.php");
        exit();
    }
} catch(PDOException $e) {
    header("Location: ../index.php");
    exit();
}

include '../componants/head.php';
include '../componants/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <?php if($product['image']): ?>
                <?php if(filter_var($product['image'], FILTER_VALIDATE_URL)): ?>
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                         class="img-fluid rounded shadow">
                <?php else: ?>
                    <img src="../assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                         class="img-fluid rounded shadow">
                <?php endif; ?>
            <?php else: ?>
                <img src="../assets/images/no-image.jpg" 
                     alt="No Image Available"
                     class="img-fluid rounded shadow">
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h1 class="mb-4"><?php echo htmlspecialchars($product['product_name']); ?></h1>
            <div class="fs-2 text-primary mb-4">
                $<?php echo number_format($product['price'], 2); ?>
            </div>
            <div class="mb-4">
                <h4>Description</h4>
                <p class="lead"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            <?php if(isset($_SESSION['logged_in'])): ?>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="input-group" style="width: 130px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(-1)">-</button>
                        <input type="number" id="quantity" class="form-control text-center" value="1" min="1" max="10">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(1)">+</button>
                    </div>
                </div>
                <button class="btn btn-primary btn-lg" onclick="addToCart(<?php echo $product['id']; ?>)">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                </button>
            <?php else: ?>
                <a href="../auth/login.php" class="btn btn-primary btn-lg">
                    Login to Purchase
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function updateQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    let newValue = parseInt(quantityInput.value) + change;
    if (newValue >= 1 && newValue <= 10) {
        quantityInput.value = newValue;
    }
}

function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;
    
    fetch('../functions/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'cart.php';
        } else {
            alert(data.message || 'Failed to add product to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding to cart');
    });
}
</script>

<?php include '../componants/footer.php'; ?>