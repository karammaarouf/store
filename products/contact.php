<?php
session_start();
require_once '../functions/connect.php';
include '../componants/head.php';
include '../componants/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Contact Us</h2>
                    
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                            <?php 
                                echo $_SESSION['message'];
                                unset($_SESSION['message']);
                                unset($_SESSION['message_type']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="../functions/process_contact.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?php echo isset($_SESSION['logged_in']) ? $_SESSION['username'] : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4 shadow">
                <div class="card-body">
                    <h3 class="card-title mb-4">Other Ways to Reach Us</h3>
                    
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <i class="bi bi-geo-alt fs-2 text-primary"></i>
                            <h5 class="mt-2">Address</h5>
                            <p>123 Store Street<br>City, Country 12345</p>
                        </div>
                        
                        <div class="col-md-4 text-center mb-3">
                            <i class="bi bi-telephone fs-2 text-primary"></i>
                            <h5 class="mt-2">Phone</h5>
                            <p>+1 234 567 8900</p>
                        </div>
                        
                        <div class="col-md-4 text-center mb-3">
                            <i class="bi bi-envelope fs-2 text-primary"></i>
                            <h5 class="mt-2">Email</h5>
                            <p>contact@store.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../componants/footer.php'; ?>