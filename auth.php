<?php
session_start();
include("db.php");

$action = $_POST['action'];

if ($action === 'login') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' OR email='$username' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.html");
        }
    } else {
        echo "❌ Invalid credentials.<br><br><a href='auth.html'><button style='padding:8px 18px; background:#f44336; color:white; border:none; border-radius:5px; font-size:16px; cursor:pointer;'>Go Back</button></a>";
    }

} elseif ($action === 'register') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        echo "❌ Passwords do not match.";
        exit();
    }

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $existing = mysqli_fetch_assoc($check);
        if ($existing['username'] === $username) {
            echo "❌ Username already exists.";
        } elseif ($existing['email'] === $email) {
            echo "❌ Email already exists.";
        } else {
            echo "❌ Username or email already exists.";
        }
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user';

    $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$hashed_password', '$role')";
    if (mysqli_query($conn, $query)) {
        echo "✅ Registration successful.";
        exit();
    } else {
        echo "❌ Registration failed: " . mysqli_error($conn);
    }
}
?>