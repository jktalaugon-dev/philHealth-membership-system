<?php
include "config.php";
session_start();

// Access Control - Normal User only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: admin_login.php");
    exit();
}

$pin_id = $_SESSION['pin_id'];

// Fetch just the member's core details
$query = "SELECT m.*, mc.Member_Type, mc.Contributor_Type 
          FROM MEMBER_T m 
          LEFT JOIN MEMBER_CLASSIFICATION mc ON m.Contributor_ID = mc.Contributor_ID 
          WHERE m.PIN_ID = ?";

$stmt = $conn->prepare($query);
// FIX: Use the full string ($pin_id) and bind it as a string ("s")
$stmt->bind_param("s", $pin_id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Dashboard - PhilHealth Portal</title>
    <link rel="stylesheet" href="css/userdash.css">
    <link href='https://fonts.googleapis.com/css?family=Alan Sans' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald|Noto+Sans">
</head>
<body>
<div class="top-navbar">
    <img src="pic1.png" alt="PhilHealth Logo" class="header-logo">
    <div class="nav-buttons">
        <a href="user_edit.php" class="btn-edit">Edit Profile</a>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</div>
<div class="dashboard-container">
    <h2>Welcome back, <?= htmlspecialchars($_SESSION['fullname']) ?>!</h2>
    
    <div class="card">
        <h3>My Membership Identity</h3>
        <div class="info-grid">
            <div><div class="info-label">Assigned PIN ID</div><div class="info-value"><?= htmlspecialchars($_SESSION['pin_id']) ?></div></div>
            <div><div class="info-label">Account Username</div><div class="info-value"><?= htmlspecialchars($_SESSION['username']) ?></div></div>
        </div>
    </div>

    <?php if($member): ?>
    <div class="card">
        <h3>Membership Overview</h3>
        <div class="info-grid">
            <div><div class="info-label">Full Name</div><div class="info-value"><?= htmlspecialchars($member['Member_Name']) ?></div></div>
            <div><div class="info-label">Date of Birth</div><div class="info-value"><?= htmlspecialchars($member['Date_of_Birth']) ?></div></div>
            <div><div class="info-label">Member Type</div><div class="info-value"><?= htmlspecialchars($member['Member_Type']) ?></div></div>
            <div><div class="info-label">Contributor Type</div><div class="info-value"><?= htmlspecialchars($member['Contributor_Type']) ?></div></div>
        </div>
    </div>
    <?php else: ?>
        <div class="card" style="text-align: center; color: #b91c1c;">
            Error loading detailed membership data.
        </div>
    <?php endif; ?>
</div>
</body>
</html>