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
                <h2>Users Management</h2>
            </div>

            <!-- Users Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                        while ($user = $stmt->fetch()) {
                        ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary'; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo $user['created_at']; ?></td>
                                <td>
                                    <?php if($user['id'] !== $_SESSION['user_id']): ?>
                                        <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    <?php endif; ?>

<script>
function deleteUser(userId) {
    if(confirm('Are you sure you want to delete this user?')) {
        fetch('../functions/delete_user.php?id=' + userId)
            .then(response => response.text())
            .then(() => {
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete user');
            });
    }
}
</script>
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
function changeRole(userId, currentRole) {
    const newRole = currentRole === 'admin' ? 'user' : 'admin';
    if(confirm(`Are you sure you want to change this user's role to ${newRole}?`)) {
        window.location.href = `../functions/change_role.php?id=${userId}&role=${newRole}`;
    }
}
</script>