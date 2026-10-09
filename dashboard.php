<?php
include "config.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$query = "SELECT 
    m.PIN_ID, k.Konsulta_Provider, m.Member_Name, m.MotherMaiden_Name, m.Spouse_Name, m.Date_of_Birth, m.Place_of_Birth, m.Sex, m.Civil_Status, m.Member_Citizenship, m.Philsys_ID, m.TIN_ID,
    c.Permanent_Home_Add, c.Mailing_Add, c.HomePhone_Num, c.Mobile_Num, c.BusinessD_Line, c.Email_Add,
    GROUP_CONCAT(d.DepName SEPARATOR '\n\n') AS DepName,
    GROUP_CONCAT(d.Relationship SEPARATOR '\n\n') AS Relationship,
    GROUP_CONCAT(d.Dependent_Date_of_Birth SEPARATOR '\n\n') AS Dependent_Date_of_Birth,
    GROUP_CONCAT(d.Dependent_Citizenship SEPARATOR '\n\n') AS Dependent_Citizenship,
    GROUP_CONCAT(d.Dependent_With_Permanent_Disability SEPARATOR '\n\n') AS Dependent_With_Permanent_Disability,
    mc.Member_Type, mc.Contributor_Type, mc.Profession, mc.Monthly_Income, mc.Proof_of_Income, mc.Financial_Incapable
FROM MEMBER_T m
LEFT JOIN KONSULTA_PROVIDER k ON m.Provider_ID = k.Provider_ID
LEFT JOIN CONTACT c ON m.Contact_ID = c.Contact_ID
LEFT JOIN MEMBER_CLASSIFICATION mc ON m.Contributor_ID = mc.Contributor_ID
LEFT JOIN DEPENDENT d ON m.PIN_ID = d.PIN_ID
GROUP BY m.PIN_ID
ORDER BY m.PIN_ID DESC";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link href='https://fonts.googleapis.com/css?family=Alan Sans' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald|Noto+Sans">
</head>
<body>
<div class="top-navbar">
    <img src="pic1.png" alt="PhilHealth Logo" class="header-logo">
    <div class="nav-controls"><a href="index.php" class="btn-back">Registration Form</a><a href="logout.php" class="btn-logout">Logout</a></div>
</div>
<div class="dashboard-container">
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>PIN_ID</th><th>Konsulta_Provider</th><th>Member_Name</th><th>MotherMaiden_Name</th><th>Spouse_Name</th>
                    <th>Date_of_Birth</th><th>Place_of_Birth</th><th>Sex</th><th>Civil_Status</th><th>Member_Citizenship</th>
                    <th>Philsys_ID</th><th>TIN_ID</th><th>Permanent_Home_Add</th><th>Mailing_Add</th><th>HomePhone_Num</th>
                    <th>Mobile_Num</th><th>BusinessD_Line</th><th>Email_Add</th>
                    <th>DepName</th><th>Relationship</th><th>Dependent_Date_of_Birth</th><th>Dependent_Citizenship</th><th>Dependent_With_Permanent_Disability</th>
                    <th>Member_Type</th><th>Contributor_Type</th><th>Profession</th><th>Monthly_Income</th><th>Proof_of_Income</th><th>Financial_Incapable</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['PIN_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Konsulta_Provider'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['Member_Name']) ?></td>
                        <td><?= htmlspecialchars($row['MotherMaiden_Name']) ?></td>
                        <td><?= htmlspecialchars($row['Spouse_Name'] ?: 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['Date_of_Birth']) ?></td>
                        <td><?= htmlspecialchars($row['Place_of_Birth']) ?></td>
                        <td><?= htmlspecialchars($row['Sex']) ?></td>
                        <td><?= htmlspecialchars($row['Civil_Status']) ?></td>
                        <td><?= htmlspecialchars($row['Member_Citizenship']) ?></td>
                        <td><?= htmlspecialchars($row['Philsys_ID'] ?: 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['TIN_ID'] ?: 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['Permanent_Home_Add']) ?></td>
                        <td><?= htmlspecialchars($row['Mailing_Add']) ?></td>
                        <td><?= htmlspecialchars($row['HomePhone_Num'] ?: 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['Mobile_Num'] ?: 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['BusinessD_Line'] ?: 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['Email_Add'] ?: 'N/A') ?></td>
                        <td style="white-space: pre-wrap;"><?= htmlspecialchars($row['DepName'] ?: 'None') ?></td>
                        <td style="white-space: pre-wrap;"><?= htmlspecialchars($row['Relationship'] ?: 'None') ?></td>
                        <td style="white-space: pre-wrap;"><?= htmlspecialchars($row['Dependent_Date_of_Birth'] ?: 'None') ?></td>
                        <td style="white-space: pre-wrap;"><?= htmlspecialchars($row['Dependent_Citizenship'] ?: 'None') ?></td>
                        <td style="white-space: pre-wrap;"><?= htmlspecialchars($row['Dependent_With_Permanent_Disability'] ?: 'None') ?></td>
                        <td><?= htmlspecialchars($row['Member_Type']) ?></td>
                        <td><?= htmlspecialchars($row['Contributor_Type']) ?></td>
                        <td><?= htmlspecialchars($row['Profession'] ?: 'N/A') ?></td>
                        <td>₱<?= number_format($row['Monthly_Income'] ?? 0, 2) ?></td>
                        <td><?= htmlspecialchars($row['Proof_of_Income'] ?: 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['Financial_Incapable'] ?? 'N/A') ?></td>
                        <td class="action-cell">
                            <a class="btn-action btn-delete" href="delete.php?pin=<?= urlencode($row['PIN_ID']) ?>" onclick="return confirm('Delete permanently?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>