<?php
session_start();
include "config.php";

// Access Control - Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['pin'])) {
    $targetPin = trim($_GET['pin']);

    try {
        $conn->begin_transaction();

        // Pull reference attributes
        $findStmt = $conn->prepare("SELECT Contact_ID, Contributor_ID FROM MEMBER_T WHERE PIN_ID = ?");
        $findStmt->bind_param("s", $targetPin);
        $findStmt->execute();
        $res = $findStmt->get_result()->fetch_assoc();
        $findStmt->close();

        if ($res) {
            $cid = $res['Contact_ID'];
            $ctid = $res['Contributor_ID'];

            // Delete dependents first (foreign key constraint)
            $delDeps = $conn->prepare("DELETE FROM DEPENDENT WHERE PIN_ID = ?");
            $delDeps->bind_param("s", $targetPin);
            $delDeps->execute();
            $delDeps->close();

            // Delete member
            $delMember = $conn->prepare("DELETE FROM MEMBER_T WHERE PIN_ID = ?");
            $delMember->bind_param("s", $targetPin);
            $delMember->execute();
            $delMember->close();

            // Clean up standalone records
            $conn->query("DELETE FROM CONTACT WHERE Contact_ID = $cid");
            $conn->query("DELETE FROM MEMBER_CLASSIFICATION WHERE Contributor_ID = $ctid");
        }

        $conn->commit();
    } catch (Exception $ex) {
        $conn->rollback();
    }
}

header("Location: dashboard.php");
exit();
?>