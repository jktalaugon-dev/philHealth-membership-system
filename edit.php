<?php
session_start();
include "config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}
if (!isset($_GET['pin'])) {
    header("Location: dashboard.php");
    exit();
}

$targetPin = trim($_GET['pin']);

$loadSql = "SELECT m.*, c.*, mc.*, k.Konsulta_Provider, k.Provider_ID as ProvID 
            FROM MEMBER_T m 
            JOIN CONTACT c ON m.Contact_ID = c.Contact_ID
            JOIN MEMBER_CLASSIFICATION mc ON m.Contributor_ID = mc.Contributor_ID 
            LEFT JOIN KONSULTA_PROVIDER k ON m.Provider_ID = k.Provider_ID
            WHERE m.PIN_ID = ?";
$lStmt = $conn->prepare($loadSql);
$lStmt->bind_param("s", $targetPin);
$lStmt->execute();
$memberData = $lStmt->get_result()->fetch_assoc();
$lStmt->close();

if (!$memberData) {
    die("Error: Member profile not found.");
}

$depSql = "SELECT * FROM DEPENDENT WHERE PIN_ID = ?";
$depStmt = $conn->prepare($depSql);
$depStmt->bind_param("s", $targetPin);
$depStmt->execute();
$dependents = $depStmt->get_result();
$depStmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();

        $updateContact = $conn->prepare("UPDATE CONTACT SET Permanent_Home_Add = ?, Mailing_Add = ?, HomePhone_Num = ?, Mobile_Num = ?, BusinessD_Line = ?, Email_Add = ? WHERE Contact_ID = ?");
        $updateContact->bind_param("ssssssi", $_POST['permanent_home_add'], $_POST['mailing_add'], $_POST['home_phone_num'], $_POST['mobile_num'], $_POST['business_d_line'], $_POST['email_add'], $memberData['Contact_ID']);
        $updateContact->execute();
        $updateContact->close();

        $income = !empty($_POST['monthly_income']) ? floatval($_POST['monthly_income']) : 0;
        $financially_incapable = ($income > 10000) ? 'NO' : 'YES';

        $updateClass = $conn->prepare("UPDATE MEMBER_CLASSIFICATION SET Member_Type = ?, Contributor_Type = ?, Profession = ?, Monthly_Income = ?, Proof_of_Income = ?, Financial_Incapable = ? WHERE Contributor_ID = ?");
        $updateClass->bind_param("sssdssi", $_POST['member_type'], $_POST['contributor_type'], $_POST['profession'], $income, $_POST['proof_of_income'], $financially_incapable, $memberData['Contributor_ID']);
        $updateClass->execute();
        $updateClass->close();

        // Konsulta Provider Check (Saves NULL if blank/na)
        $konsultaProvider = !empty($_POST['konsulta_provider']) ? trim($_POST['konsulta_provider']) : '';
        $provider_id = NULL;

        if ($konsultaProvider !== '' && strtolower($konsultaProvider) !== 'n/a') {
            $checkProvider = $conn->prepare("SELECT Provider_ID FROM KONSULTA_PROVIDER WHERE Konsulta_Provider = ?");
            $checkProvider->bind_param("s", $konsultaProvider);
            $checkProvider->execute();
            $providerResult = $checkProvider->get_result();
            
            if ($providerResult->num_rows > 0) {
                $providerRow = $providerResult->fetch_assoc();
                $provider_id = $providerRow['Provider_ID'];
            } else {
                $insertProvider = $conn->prepare("INSERT INTO KONSULTA_PROVIDER (Konsulta_Provider) VALUES (?)");
                $insertProvider->bind_param("s", $konsultaProvider);
                $insertProvider->execute();
                $provider_id = $conn->insert_id;
                $insertProvider->close();
            }
            $checkProvider->close();
        }

        $updateMember = $conn->prepare("UPDATE MEMBER_T SET Member_Name = ?, MotherMaiden_Name = ?, Spouse_Name = ?, Date_of_Birth = ?, Place_of_Birth = ?, Sex = ?, Civil_Status = ?, Member_Citizenship = ?, Philsys_ID = ?, TIN_ID = ?, Provider_ID = ? WHERE PIN_ID = ?");
        $updateMember->bind_param("ssssssssssis", $_POST['member_name'], $_POST['mother_maiden'], $_POST['spouse_name'], $_POST['date_of_birth'], $_POST['place_of_birth'], $_POST['sex'], $_POST['civil_status'], $_POST['member_citizenship'], $_POST['philsys_id'], $_POST['tin_id'], $provider_id, $targetPin);
        $updateMember->execute();
        $updateMember->close();

        if (isset($_POST['keep_dependents'])) {
            $keepIds = $_POST['keep_dependents'];
            $deleteSql = "DELETE FROM DEPENDENT WHERE PIN_ID = ? AND Dep_ID NOT IN (" . implode(',', array_fill(0, count($keepIds), '?')) . ")";
            $deleteStmt = $conn->prepare($deleteSql);
            $params = array_merge([$targetPin], $keepIds);
            $deleteStmt->bind_param(str_repeat('s', count($params)), ...$params);
            $deleteStmt->execute();
            $deleteStmt->close();
        } else {
             $conn->query("DELETE FROM DEPENDENT WHERE PIN_ID = $targetPin");
        }

        if (isset($_POST['new_dep_name']) && is_array($_POST['new_dep_name'])) {
            $newDepSql = "INSERT INTO DEPENDENT (PIN_ID, DepName, Relationship, Dependent_Date_of_Birth, Dependent_Citizenship, Dependent_With_Permanent_Disability) VALUES (?, ?, ?, ?, ?, ?)";
            $newDepStmt = $conn->prepare($newDepSql);
            
            for ($i = 0; $i < count($_POST['new_dep_name']); $i++) {
                if (!empty(trim($_POST['new_dep_name'][$i]))) {
                    $newDepStmt->bind_param("ssssss", $targetPin, $_POST['new_dep_name'][$i], $_POST['new_dep_relationship'][$i], $_POST['new_dep_birth'][$i], $_POST['new_dep_citizenship'][$i], $_POST['new_dep_disability'][$i]);
                    $newDepStmt->execute();
                }
            }
            $newDepStmt->close();
        }

        $conn->commit();
        header("Location: dashboard.php");
        exit();

    } catch (Exception $ex) {
        $conn->rollback();
        die("Update failed: " . $ex->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Member Profile</title>
    <link rel="stylesheet" href="css/edit.css">
    <link href='https://fonts.googleapis.com/css?family=Alan Sans' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald|Noto+Sans">
</head>
<body>
   
<div class="edit-container"> 
    <h2>EDIT MEMBER PROFILE</h2>
     <div class="top-navbar">
    <img src="pic1.png" alt="PhilHealth Logo" class="header-logo">
    </div>

    <div class="pin-display">PIN: <?= htmlspecialchars($targetPin) ?></div>
    
    <form method="POST">
        <div class="form-grid">
            <div class="form-group full-width"><label>Member Name</label><input type="text" name="member_name" value="<?= htmlspecialchars($memberData['Member_Name']) ?>" required></div>
            <div class="form-group full-width"><label>Mother's Maiden Name</label><input type="text" name="mother_maiden" value="<?= htmlspecialchars($memberData['MotherMaiden_Name']) ?>" required></div>
            <div class="form-group full-width"><label>Spouse Name</label><input type="text" name="spouse_name" value="<?= htmlspecialchars($memberData['Spouse_Name'] ?? '') ?>"></div>
            <div class="form-group"><label>Date of Birth</label><input type="date" name="date_of_birth" value="<?= $memberData['Date_of_Birth'] ?>" required></div>
            <div class="form-group"><label>Place of Birth</label><input type="text" name="place_of_birth" value="<?= htmlspecialchars($memberData['Place_of_Birth']) ?>" required></div>
            <div class="form-group"><label>Sex</label><select name="sex"><option value="M" <?= $memberData['Sex'] === 'M' ? 'selected' : '' ?>>Male</option><option value="F" <?= $memberData['Sex'] === 'F' ? 'selected' : '' ?>>Female</option></select></div>
            <div class="form-group"><label>Civil Status</label><select name="civil_status">
                <option value="Single" <?= $memberData['Civil_Status'] === 'Single' ? 'selected' : '' ?>>Single</option>
                <option value="Married" <?= $memberData['Civil_Status'] === 'Married' ? 'selected' : '' ?>>Married</option>
                <option value="Widower" <?= $memberData['Civil_Status'] === 'Widower' ? 'selected' : '' ?>>Widower</option>
                <option value="Legally Separated" <?= $memberData['Civil_Status'] === 'Legally Separated' ? 'selected' : '' ?>>Legally Separated</option>
                <option value="Annulled" <?= $memberData['Civil_Status'] === 'Annulled' ? 'selected' : '' ?>>Annulled</option>
            </select></div>
            <div class="form-group"><label>Citizenship</label><input type="text" name="member_citizenship" value="<?= htmlspecialchars($memberData['Member_Citizenship']) ?>" required></div>
            <div class="form-group"><label>PhilSys ID</label><input type="text" name="philsys_id" value="<?= htmlspecialchars($memberData['Philsys_ID']) ?>"></div>
            <div class="form-group"><label>TIN ID</label><input type="text" name="tin_id" value="<?= htmlspecialchars($memberData['TIN_ID']) ?>"></div>
            <div class="form-group full-width"><label>Permanent Address</label><input type="text" name="permanent_home_add" value="<?= htmlspecialchars($memberData['Permanent_Home_Add']) ?>" required></div>
            <div class="form-group full-width"><label>Mailing Address</label><input type="text" name="mailing_add" value="<?= htmlspecialchars($memberData['Mailing_Add']) ?>" required></div>
            <div class="form-group"><label>Home Phone</label><input type="text" name="home_phone_num" value="<?= htmlspecialchars($memberData['HomePhone_Num']) ?>"></div>
            <div class="form-group"><label>Mobile</label><input type="text" name="mobile_num" value="<?= htmlspecialchars($memberData['Mobile_Num']) ?>" required></div>
            <div class="form-group"><label>Business Line</label><input type="text" name="business_d_line" value="<?= htmlspecialchars($memberData['BusinessD_Line']) ?>"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email_add" value="<?= htmlspecialchars($memberData['Email_Add']) ?>"></div>
            
            <div class="form-group">
                <label>Member Type</label>
                <select name="member_type" id="member_type" required>
                    <option value="Direct Contributor" <?= $memberData['Member_Type'] === 'Direct Contributor' ? 'selected' : '' ?>>Direct Contributor</option>
                    <option value="Indirect Contributor" <?= $memberData['Member_Type'] === 'Indirect Contributor' ? 'selected' : '' ?>>Indirect Contributor</option>
                </select>
            </div>
            <div class="form-group">
                <label>Contributor Type</label>
                <select name="contributor_type" id="contributor_type" data-selected="<?= htmlspecialchars($memberData['Contributor_Type']) ?>" required>
                    </select>
            </div>

            <div class="form-group"><label>Profession</label><input type="text" name="profession" value="<?= htmlspecialchars($memberData['Profession']) ?>"></div>
            <div class="form-group"><label>Monthly Income</label><input type="number" step="0.01" name="monthly_income" value="<?= $memberData['Monthly_Income'] ?>"></div>
            <div class="form-group"><label>Proof of Income</label><input type="text" name="proof_of_income" value="<?= htmlspecialchars($memberData['Proof_of_Income']) ?>"></div>
            <div class="form-group full-width"><label>Preferred Konsulta Provider (Leave it as blank if none)</label><input type="text" name="konsulta_provider" value="<?= htmlspecialchars($memberData['Konsulta_Provider'] ?? '') ?>"></div>
        </div>

        <div class="section-title">Existing Dependents</div>
        <?php if ($dependents->num_rows > 0): ?>
            <?php while($dep = $dependents->fetch_assoc()): ?>
                <div class="dependent-card">
                    <div class="dependent-header">
                        <strong><?= htmlspecialchars($dep['DepName']) ?></strong>
                        <label><input type="checkbox" name="keep_dependents[]" value="<?= $dep['Dep_ID'] ?>" checked> Keep</label>
                    </div>
                    <div>Relationship: <?= htmlspecialchars($dep['Relationship']) ?> | DOB: <?= htmlspecialchars($dep['Dependent_Date_of_Birth']) ?> | Disability: <?= htmlspecialchars($dep['Dependent_With_Permanent_Disability']) ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No dependents registered.</p>
        <?php endif; ?>

        <div class="section-title">Add New Dependents</div>
        <div id="new_dependents_container"></div>
        <button type="button" class="btn-add-dep" onclick="addNewDependent()">+ Add New Dependent</button>

        <div class="btn-group">
            <button type="submit" class="btn-save">Save All Changes</button>
            <a href="dashboard.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
    const contributorOptions = {
        "Direct Contributor": [
            "Employed Private", "Employed Government", "Professional Practitioner",
            "Self-Earning Individual", "Kasambahay", "Migrant Worker",
            "Lifetime Member", "Filipinos with Dual Citizenship / Living Abroad",
            "Foreign National", "Family Driver"
        ],
        "Indirect Contributor": [
            "Listahanan", "4Ps/MCCT", "Senior Citizen", "PAMANA", "KIA/KIPO",
            "LGU-sponsored", "NGA-sponsored", "Private-sponsored",
            "Person with Disability", "Bangsamoro/Normalization"
        ]
    };

    function updateContributorTypes() {
        const type = document.getElementById('member_type').value;
        const subSelect = document.getElementById('contributor_type');
        const currentSelected = subSelect.getAttribute('data-selected');
        
        subSelect.innerHTML = '<option value="">-- Select Contributor Type --</option>';
        
        if (contributorOptions[type]) {
            contributorOptions[type].forEach(option => {
                const opt = document.createElement('option');
                opt.value = option;
                opt.textContent = option;
                if (option === currentSelected) {
                    opt.selected = true;
                }
                subSelect.appendChild(opt);
            });
        }
    }

    document.getElementById('member_type').addEventListener('change', function() {
        document.getElementById('contributor_type').setAttribute('data-selected', '');
        updateContributorTypes();
    });

    document.addEventListener('DOMContentLoaded', updateContributorTypes);

    let newDepIndex = 0;
    
    function addNewDependent() {
        const container = document.getElementById('new_dependents_container');
        const id = newDepIndex;
        const div = document.createElement('div');
        div.className = 'dependent-card';
        div.id = `new_dep_${id}`;
        div.innerHTML = `
            <div class="dependent-header">
                <strong>New Dependent #${id + 1}</strong>
                <button type="button" class="btn-remove-dep" onclick="document.getElementById('new_dep_${id}').remove()">Remove</button>
            </div>
            <div class="form-grid">
                <div class="form-group full-width"><label>Full Name</label><input type="text" name="new_dep_name[]" placeholder="Full Name" required></div>
                <div class="form-group"><label>Relationship</label><select name="new_dep_relationship[]"><option value="Spouse">Spouse</option><option value="Child">Child</option><option value="Parent">Parent</option></select></div>
                <div class="form-group"><label>Date of Birth</label><input type="date" name="new_dep_birth[]" required></div>
                <div class="form-group"><label>Citizenship</label><select name="new_dep_citizenship[]"><option value="Filipino">Filipino</option><option value="Dual Citizenship">Dual Citizenship</option><option value="Foreign National">Foreign National</option></select></div>
                <div class="form-group"><label>Disability</label><select name="new_dep_disability[]"><option value="NO">No Disability</option><option value="YES">Permanent Disability</option></select></div>
            </div>
        `;
        container.appendChild(div);
        newDepIndex++;
    }
</script>
</body>
</html>