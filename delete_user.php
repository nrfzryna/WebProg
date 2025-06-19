<?php
require_once("configuration.php");
session_start();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Decode JSON data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract user ID and user level from JSON data
    $userID = mysqli_real_escape_string($conn, $data['userid']);
    $userLevel = (int)$data['userlevel'];

    $userType = '';
    $userIdColumn = '';
    switch ($userLevel) {
        case 1:
            $userType = 'AdminList';
            $userIdColumn = 'AdminID';
            break;
        case 2:
            $userType = 'ManagerList';
            $userIdColumn = 'ManagerID';
            break;
        case 3:
            $userType = 'StaffList';
            $userIdColumn = 'StaffID';
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid user level specified.']);
            exit;
    }

    // Begin a transaction
    mysqli_begin_transaction($conn);

    try {
        // Delete from the specific user type table
        $sqlUserType = "DELETE FROM $userType WHERE $userIdColumn = '$userID'";
        if (!mysqli_query($conn, $sqlUserType)) {
            throw new Exception("Error deleting from $userType: " . mysqli_error($conn));
        }

        // Delete from the logindetails table
        $sqlLoginDetail = "DELETE FROM logindetails WHERE UserID = '$userID' AND UserLevel = '$userLevel'";
        if (!mysqli_query($conn, $sqlLoginDetail)) {
            throw new Exception("Error deleting from logindetails: " . mysqli_error($conn));
        }

        // Commit the transaction
        mysqli_commit($conn);

        echo json_encode(['success' => true]);
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($conn);

        echo json_encode(['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()]);
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
exit();
?>
