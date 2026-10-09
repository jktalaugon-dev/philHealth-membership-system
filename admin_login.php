<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; // Plain text from the user

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // SIMPLE CHECK: Does the typed password exactly match the database?
        if ($password === $row['password']) {
            
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['fullname'] = $row['fullname'];
            
            // Add PIN ID to session if it's a user
            if ($row['role'] === 'user') {
                $_SESSION['pin_id'] = $row['pin_id'];
                header("Location: user_dashboard.php");
            } else {
                header("Location: dashboard.php"); // Admin
            }
            exit();
        } else {
            echo "<script>alert('Invalid password!'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.location.href='login.php';</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - PhilHealth Portal</title>
    <link rel="stylesheet" href="css/adminlog.css">
    <link href='https://fonts.googleapis.com/css?family=Alan Sans' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald|Noto+Sans">
    
</head>
<body>
    <div class="login-container">
        <h2>PhilHealth Portal Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="back-link">
            <a href="index.php">← Back to Registration Form</a>
        </div>
        <div class="info-text">
            New member? <a href="index.php">Register here</a><br>
            <small>Admins have full access. Regular users can view their own records.</small>
        </div>
    </div>
</body>
</html>