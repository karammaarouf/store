<div class="container-fluid p-3 bg-white border-bottom">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="m-0">Admin Panel</h4>
        </div>
        <div class="d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-link text-dark text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-2"></i>
                    <?php echo $_SESSION['username'] ?? 'Admin'; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>