<div class="sidebar bg-light border-end" style="width: 250px; min-height: 100vh;">
    <div class="p-3">
        <!-- User Profile Section -->
        <div class="text-center mb-4">
            <img src="assets/images/default-avatar.png" class="rounded-circle" width="80" height="80" alt="User Profile">
            <h5 class="mt-2">Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?></h5>
        </div>

        <!-- Navigation Links -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="my-orders.php">
                    <i class="bi bi-bag"></i> My Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="wishlist.php">
                    <i class="bi bi-heart"></i> Wishlist
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile-settings.php">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="address-book.php">
                    <i class="bi bi-geo-alt"></i> Address Book
                </a>
            </li>
        </ul>

        <hr>

        <!-- Account Actions -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>