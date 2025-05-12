<?php
session_start();
require_once 'functions/connect.php';

if (!isset($_SESSION['logged_in']) && isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_token'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND remember_token = ? AND token_expiry > NOW()");
    $stmt->execute([$_COOKIE['remember_user'], $_COOKIE['remember_token']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
    }
}

include 'componants/head.php';
include 'componants/header.php';
include 'componants/messages.php'; // Add this line

// Fetch products from database
try {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!-- Main Content Area -->
<div class="container mt-4">
    <h1 class="mb-4">Our Products</h1>
    
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach($products as $product): ?>
            <div class="col">
                <div class="card h-100">
                    <?php if($product['image']): ?>
                        <?php if(filter_var($product['image'], FILTER_VALIDATE_URL)): ?>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="assets/images/no-image.jpg" 
                             class="card-img-top" 
                             alt="No Image Available"
                             style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['product_name']; ?></h5>
                        <p class="card-text text-truncate"><?php echo $product['description']; ?></p>
                        <h6 class="text-primary">$<?php echo number_format($product['price'], 2); ?></h6>
                    </div>
                    
                    <div class="card-footer bg-white border-top-0">
                        <a href="products/product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                        <?php if(isset($_SESSION['logged_in'])): ?>
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-success btn-sm">
                                Add to Cart
                            </button>
                        <?php else: ?>
                            <a href="auth/login.php" class="btn btn-success btn-sm">Login to Buy</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function addToCart(productId) {
    fetch('functions/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart successfully!');
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

<?php include 'componants/footer.php'; ?>