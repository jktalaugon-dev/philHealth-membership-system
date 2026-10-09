<?php
include "config.php";

// Helper function to combine names
function combineName($last, $first, $middle, $extension, $noMid, $mono) {
    if ($mono) return strtoupper(trim($last));
    $name = strtoupper(trim($last)) . ", " . strtoupper(trim($first));
    if (!$noMid && !empty(trim($middle))) $name .= " " . strtoupper(trim($middle));
    if (!empty(trim($extension))) $name .= " " . strtoupper(trim($extension));
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();
        
        // 1. Contact Info (FIX: Catch disabled Mailing Add field)
        $perm_add = $_POST['permanent_home_add'] ?? '';
        $mail_add = !empty($_POST['mailing_add']) ? $_POST['mailing_add'] : $perm_add;
        $home_phone = $_POST['home_phone_num'] ?? '';
        $mobile = $_POST['mobile_num'] ?? '';
        $bus_line = $_POST['business_d_line'] ?? '';
        $email = $_POST['email_add'] ?? '';

        $cStmt = $conn->prepare("INSERT INTO CONTACT (Permanent_Home_Add, Mailing_Add, HomePhone_Num, Mobile_Num, BusinessD_Line, Email_Add) VALUES (?, ?, ?, ?, ?, ?)");
        $cStmt->bind_param("ssssss", $perm_add, $mail_add, $home_phone, $mobile, $bus_line, $email);
        $cStmt->execute();
        $contactId = $conn->insert_id;

        // 2. Member Classification
        $income = floatval($_POST['monthly_income'] ?? 0);
        $finIncapable = ($income > 10000) ? 'NO' : 'YES';
        $memberType = $_POST['member_type'] ?? '';
        $contribType = $_POST['contributor_type'] ?? '';
        $profession = $_POST['profession'] ?? '';
        $proof = $_POST['proof_of_income'] ?? '';

        $mStmt = $conn->prepare("INSERT INTO MEMBER_CLASSIFICATION (Member_Type, Contributor_Type, Profession, Monthly_Income, Proof_of_Income, Financial_Incapable) VALUES (?, ?, ?, ?, ?, ?)");
        $mStmt->bind_param("sssdss", $memberType, $contribType, $profession, $income, $proof, $finIncapable);
        $mStmt->execute();
        $contributorId = $conn->insert_id;

        // 3. Konsulta Provider
        $provId = NULL;
        $providerName = trim($_POST['konsulta_provider'] ?? '');
        if (!empty($providerName) && strtolower($providerName) !== 'n/a') {
            $pStmt = $conn->prepare("INSERT INTO KONSULTA_PROVIDER (Konsulta_Provider) VALUES (?)");
            $pStmt->bind_param("s", $providerName);
            $pStmt->execute();
            $provId = $pStmt->insert_id;
        }

        // 4. Generate the 01012026XXXX PIN
        $lastIdQuery = "SELECT PIN_ID FROM MEMBER_T ORDER BY PIN_ID DESC LIMIT 1";
        $result = $conn->query($lastIdQuery);
        $lastRow = $result->fetch_assoc();

        if ($lastRow) {
            $lastNum = intval(substr($lastRow['PIN_ID'], 8)); 
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }
        $formattedPin = "01012026" . sprintf("%04d", $newNum);

        // 5. Insert Member (FIX: Catch disabled middle name/first name fields safely)
        $mLast = $_POST['member_last_name'] ?? '';
        $mFirst = $_POST['member_first_name'] ?? '';
        $mMid = $_POST['member_middle_name'] ?? '';
        $mExt = $_POST['member_name_extension'] ?? '';
        $mName = combineName($mLast, $mFirst, $mMid, $mExt, isset($_POST['member_no_middle_name']), isset($_POST['member_mononym']));
        
// --- EXISTING CODE ---
        $mLast = $_POST['member_last_name'] ?? '';
        $mFirst = $_POST['member_first_name'] ?? '';
        $mMid = $_POST['member_middle_name'] ?? '';
        $mExt = $_POST['member_name_extension'] ?? '';
        $mName = combineName($mLast, $mFirst, $mMid, $mExt, isset($_POST['member_no_middle_name']), isset($_POST['member_mononym']));
        
        // --- NEW FIX: Combine Mother's Maiden Name from the 4 boxes ---
        $moLast = $_POST['mother_last_name'] ?? '';
        $moFirst = $_POST['mother_first_name'] ?? '';
        $moMid = $_POST['mother_middle_name'] ?? '';
        $moExt = $_POST['mother_extension'] ?? '';
        // Combine them using the same function!
        $maiden = combineName($moLast, $moFirst, $moMid, $moExt, isset($_POST['mother_no_middle_name']), isset($_POST['mother_mononym']));

        // --- NEW FIX: Combine Spouse Name (If your form also splits the spouse name into 4 boxes) ---
        $spLast = $_POST['spouse_last_name'] ?? '';
        $spFirst = $_POST['spouse_first_name'] ?? '';
        $spMid = $_POST['spouse_middle_name'] ?? '';
        $spExt = $_POST['spouse_extension'] ?? '';
        
        $spouse = ''; // Default to blank
        if (!empty($spLast)) { // Only combine if a spouse last name was actually typed
            $spouse = combineName($spLast, $spFirst, $spMid, $spExt, isset($_POST['spouse_no_middle_name']), isset($_POST['spouse_mononym']));
        }

        // --- EXISTING CODE ---
        $dob = $_POST['date_of_birth'] ?? '';
        $pob = $_POST['place_of_birth'] ?? '';
        $sex = $_POST['sex'] ?? '';
        $civil = $_POST['civil_status'] ?? '';
        $cit = $_POST['member_citizenship'] ?? '';
        $philId = $_POST['philsys_id'] ?? '';
        $tin = $_POST['tin_id'] ?? '';

        $mStmt = $conn->prepare("INSERT INTO MEMBER_T (PIN_ID, Member_Name, MotherMaiden_Name, Spouse_Name, Date_of_Birth, Place_of_Birth, Sex, Civil_Status, Member_Citizenship, Philsys_ID, TIN_ID, Contact_ID, Contributor_ID, Provider_ID) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $mStmt->bind_param("ssssssssssiiii", $formattedPin, $mName, $maiden, $spouse, $dob, $pob, $sex, $civil, $cit, $philId, $tin, $contactId, $contributorId, $provId);
        $mStmt->execute();

        // 6. User Account Creation
$raw_username = strtolower(str_replace(' ', '_', $mName));
        $username = $raw_username;
        $u_check = $conn->query("SELECT id FROM users WHERE username = '$username'");
        if ($u_check->num_rows > 0) $username = $username . '_' . $newNum; 

        // Generate a random 10-character password
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $plain_password = substr(str_shuffle($chars), 0, 10);
        
        // WE REMOVED THE HASHING HERE!
        // We will insert $plain_password directly into the database.

        $userSql = "INSERT INTO users (username, password, role, pin_id, fullname) VALUES (?, ?, 'user', ?, ?)";
        $userStmt = $conn->prepare($userSql);
        
        // Notice we are binding $plain_password instead of $hashed_password
        $userStmt->bind_param("ssss", $username, $plain_password, $formattedPin, $mName);
        $userStmt->execute();

        // 7. Dependents
        $dependentsList = [];
        if (!empty($_POST['dep_last_name'])) {
            $dStmt = $conn->prepare("INSERT INTO DEPENDENT (PIN_ID, DepName, Relationship, Dependent_Date_of_Birth, Dependent_Citizenship, Dependent_With_Permanent_Disability) VALUES (?, ?, ?, ?, ?, ?)");
            $depNoMiddle = $_POST['dep_no_middle_name'] ?? [];
            $depMononym = $_POST['dep_mononym'] ?? [];

            for ($i = 0; $i < count($_POST['dep_last_name']); $i++) {
                if (!empty(trim($_POST['dep_last_name'][$i]))) {
                    $isNoMid = isset($depNoMiddle[$i]) && $depNoMiddle[$i] === 'on';
                    $isMono = isset($depMononym[$i]) && $depMononym[$i] === 'on';
                    
                    // Catch disabled dependent inputs safely
                    $dLast = $_POST['dep_last_name'][$i] ?? '';
                    $dFirst = $_POST['dep_first_name'][$i] ?? '';
                    $dMid = $_POST['dep_middle_name'][$i] ?? '';
                    $dExt = $_POST['dep_extension'][$i] ?? '';

                    $dName = combineName($dLast, $dFirst, $dMid, $dExt, $isNoMid, $isMono);
                    
                    $dRel = $_POST['dep_relationship'][$i] ?? '';
                    $dBirth = $_POST['dep_birth'][$i] ?? '';
                    $dCit = $_POST['dep_citizenship'][$i] ?? '';
                    $dDis = $_POST['dep_disability'][$i] ?? '';

                    $dStmt->bind_param("ssssss", $formattedPin, $dName, $dRel, $dBirth, $dCit, $dDis);
                    $dStmt->execute();
                    $dependentsList[] = ['name' => $dName, 'relationship' => $dRel, 'birth_date' => $dBirth, 'citizenship' => $dCit, 'disability' => $dDis];
                }
            }
        }

        $conn->commit();
        session_start();
        
        // Pass everything to the confirmation screen safely
        $_SESSION['registration_data'] = [
            'pin' => $formattedPin, 'username' => $username, 'password' => $plain_password, 'philsys_id' => $philId, 'tin_id' => $tin,
            'member_name' => $mName, 'mother_maiden' => $maiden, 'spouse_name' => $spouse, 'birth_date' => $dob,
            'birth_place' => $pob, 'sex' => $sex == 'M' ? 'Male' : 'Female', 'civil_status' => $civil, 'citizenship' => $cit,
            'permanent_address' => $perm_add, 'mailing_address' => $mail_add, 'mobile' => $mobile, 'home_phone' => $home_phone,
            'business_line' => $bus_line, 'email' => $email, 'member_type' => $memberType, 'contributor_type' => $contribType,
            'profession' => $profession, 'monthly_income' => $income, 'proof_of_income' => $proof,
            'financially_incapable' => $finIncapable, 'konsulta_provider' => $providerName, 'dependents' => $dependentsList
        ];
        header("Location: confirmation.php");
    } catch (Exception $e) { 
        $conn->rollback(); 
        die("<h2 style='color:red;text-align:center;'>System Error</h2><p style='text-align:center;'>" . $e->getMessage() . "</p><br><div style='text-align:center;'><a href='index.php'>Back to Registry Form</a></div>"); 
    }
}
?>