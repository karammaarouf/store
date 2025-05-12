<?php
// Add this at the top of the file
$base_url = '/STORE';
?>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $base_url; ?>/index.php">Store</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>/index.php">Home</a>
                    </li>
                        <a class="nav-link" href="<?php echo $base_url; ?>/products/contact.php">Contact Us</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?php echo $_SESSION['username']; ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>/products/cart.php">
                                <i class="bi bi-cart"></i> Cart
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>/products/account.php">
                                <i class="bi bi-person"></i> Account
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="<?php echo $base_url; ?>/auth/logout.php">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>/auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>/auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>