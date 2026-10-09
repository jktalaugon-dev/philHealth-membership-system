<?php
session_start();

if (!isset($_SESSION['registration_data'])) {
    header("Location: index.php");
    exit();
}

$data = $_SESSION['registration_data'];
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Confirmation - PhilHealth</title>
    <link rel="stylesheet" href="css/confirmation.css">
    <link href='https://fonts.googleapis.com/css?family=Alan Sans' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald|Noto+Sans">
</head>
<body>
<div class="confirmation-container">
    <div class="success-header">
        <h1>Registration Successfully Processed!</h1>
        <div class="pin">Assigned PIN ID: <?= htmlspecialchars($data['pin']) ?></div>
        <p>Your PhilHealth Identification Number (PIN) is your unique and permanent number.<br>Always use your PIN in all transactions with PhilHealth.</p>
    </div>

    <hr class="green-line">

    <div class="credentials-box">
        <h4>Your Portal Login Credentials</h4>
        <div class="cred-line">Username: <strong><?= htmlspecialchars($data['username']) ?></strong></div>
        <div class="cred-line">Password: <span class="password-text"><?= htmlspecialchars($data['password']) ?></span></div>
        <div class="cred-warning">Please save these credentials. You will need them to log in to the portal.</div>
    </div>

    <div class="info-section">
        <h3>Personal Details</h3>
        <div class="info-grid">
            <div class="info-item full-width"><span class="info-label">Member Full Name:</span><span class="info-value"><?= htmlspecialchars($data['member_name']) ?></span></div>
            <div class="info-item full-width"><span class="info-label">Mother's Maiden Name:</span><span class="info-value"><?= htmlspecialchars($data['mother_maiden']) ?></span></div>
            <div class="info-item full-width"><span class="info-label">Spouse Name:</span><span class="info-value"><?= htmlspecialchars($data['spouse_name']) ?></span></div>
            <div class="info-item"><span class="info-label">Sex:</span><span class="info-value"><?= htmlspecialchars($data['sex']) ?></span></div>
            <div class="info-item"><span class="info-label">Civil Status:</span><span class="info-value"><?= htmlspecialchars($data['civil_status']) ?></span></div>
            <div class="info-item"><span class="info-label">Date of Birth:</span><span class="info-value"><?= htmlspecialchars($data['birth_date']) ?></span></div>
            <div class="info-item"><span class="info-label">Place of Birth:</span><span class="info-value"><?= htmlspecialchars($data['birth_place']) ?></span></div>
            <div class="info-item"><span class="info-label">Citizenship:</span><span class="info-value"><?= htmlspecialchars($data['citizenship']) ?></span></div>
            <div class="info-item"><span class="info-label">PhilSys ID:</span><span class="info-value"><?= htmlspecialchars($data['philsys_id']) ?></span></div>
            <div class="info-item"><span class="info-label">TIN ID:</span><span class="info-value"><?= htmlspecialchars($data['tin_id']) ?></span></div>
            <div class="info-item full-width"><span class="info-label">Preferred Konsulta Provider:</span><span class="info-value"><?= htmlspecialchars($data['konsulta_provider']) ?></span></div>
        </div>
    </div>

    <div class="info-section">
        <h3>Address & Contact Details</h3>
        <div class="info-grid">
            <div class="info-item full-width"><span class="info-label">Permanent Address:</span><span class="info-value"><?= htmlspecialchars($data['permanent_address']) ?></span></div>
            <div class="info-item full-width"><span class="info-label">Mailing Address:</span><span class="info-value"><?= htmlspecialchars($data['mailing_address']) ?></span></div>
            <div class="info-item"><span class="info-label">Mobile Number:</span><span class="info-value"><?= htmlspecialchars($data['mobile']) ?></span></div>
            <div class="info-item"><span class="info-label">Home Phone:</span><span class="info-value"><?= htmlspecialchars($data['home_phone']) ?></span></div>
            <div class="info-item"><span class="info-label">Business Line:</span><span class="info-value"><?= htmlspecialchars($data['business_line']) ?></span></div>
            <div class="info-item"><span class="info-label">Email Address:</span><span class="info-value"><?= htmlspecialchars($data['email']) ?></span></div>
        </div>
    </div>

    <div class="info-section">
        <h3>Membership Classification</h3>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Member Type:</span><span class="info-value"><?= htmlspecialchars($data['member_type']) ?></span></div>
            <div class="info-item full-width"><span class="info-label">Contributor Type:</span><span class="info-value"><?= htmlspecialchars($data['contributor_type']) ?></span></div>
            <div class="info-item"><span class="info-label">Profession:</span><span class="info-value"><?= htmlspecialchars($data['profession'] ?: 'N/A') ?></span></div>
            <div class="info-item"><span class="info-label">Monthly Income:</span><span class="info-value">₱<?= number_format($data['monthly_income'], 2) ?></span></div>
            <div class="info-item"><span class="info-label">Proof of Income:</span><span class="info-value"><?= htmlspecialchars($data['proof_of_income'] ?: 'N/A') ?></span></div>
            <div class="info-item"><span class="info-label">Financially Incapable:</span><span class="info-value"><?= htmlspecialchars($data['financially_incapable']) ?></span></div>
        </div>
    </div>

    <div class="info-section">
        <h3>Declared Dependents</h3>
        <?php if (!empty($data['dependents']) && count($data['dependents']) > 0): ?>
            <table class="dependent-table">
                <thead>
                    <tr><th>Full Name</th><th>Relationship</th><th>Date of Birth</th><th>Citizenship</th><th>Permanent Disability</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($data['dependents'] as $dep): ?>
                        <tr>
                            <td><?= htmlspecialchars($dep['name']) ?></td>
                            <td><?= htmlspecialchars($dep['relationship']) ?></td>
                            <td><?= htmlspecialchars($dep['birth_date']) ?></td>
                            <td><?= htmlspecialchars($dep['citizenship']) ?></td>
                            <td><?= htmlspecialchars($dep['disability']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-dependents">No dependents declared.</div>
        <?php endif; ?>
    </div>

    <div class="text-center">
        <a href="index.php" class="btn-home">Return to Registration Form</a>
        <a href="admin_login.php" class="btn-home" style="background:#475569; margin-left:10px;">Go to Login Portal</a>
    </div>
</div>
</body>
</html>