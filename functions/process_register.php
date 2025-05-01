<?php
session_start();
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: ../auth/register.php");
        exit();
    }

    try {
        // Check if email already exists
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->execute([$email]);
        
        if ($check_email->rowCount() > 0) {
            $_SESSION['error'] = "Email already exists!";
            header("Location: ../auth/register.php");
            exit();
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);

        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: ../auth/login.php");
        exit();

    } catch(PDOException $e) {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: ../auth/register.php");
        exit();
    }
} else {
    header("Location: ../auth/register.php");
    exit();
}
?>