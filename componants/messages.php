<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <?php if ($_SESSION['message_type'] === 'success'): ?>
                <i class="bi bi-check-circle-fill me-2"></i>
            <?php elseif ($_SESSION['message_type'] === 'danger'): ?>
                <i class="bi bi-exclamation-circle-fill me-2"></i>
            <?php elseif ($_SESSION['message_type'] === 'warning'): ?>
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php else: ?>
                <i class="bi bi-info-circle-fill me-2"></i>
            <?php endif; ?>
            <?php echo $_SESSION['message']; ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    ?>
<?php endif; ?>