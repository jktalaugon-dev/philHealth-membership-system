<?php include "config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PhilHealth Membership Search</title>
    <link rel="stylesheet" href="css/query.css">
    <link href='https://fonts.googleapis.com/css?family=Alan Sans' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald|Noto+Sans">
</head>
<body>
<div class="search-container">
    <a href="index.php" class="back-home">← Return to PhilHealth Registry Form</a>
    <h2>Search Membership</h2>
    <form method="GET">
        <div class="search-box">
            <input type="text" name="term" placeholder="Enter PIN Number to verify..." required value="<?= isset($_GET['term']) ? htmlspecialchars($_GET['term']) : '' ?>">
            <button type="submit">Search Record</button>
        </div>
    </form>

    <?php
    if (isset($_GET['term'])) {
        $search = trim($_GET['term']);
        $wildcard = "%" . $search . "%";
        
        $query = "SELECT PIN_ID, Member_Name FROM MEMBER_T WHERE PIN_ID = ? OR Member_Name LIKE ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $search, $wildcard);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='result-card'>   
                            <strong style='color: #008F4D;'>" . htmlspecialchars($row['Member_Name']) . "</strong>
                            <span class='badge'>PIN: " . htmlspecialchars($row['PIN_ID']) . "</span>
                      </div>";
            }
        } else {
            echo "<div class='no-result'>There are no matching records for the information you have entered.</div>";
        }
        $stmt->close();
    }
    ?>
</div>
</body>
</html>