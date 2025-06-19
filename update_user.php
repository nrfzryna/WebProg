<?php
require_once("configuration.php");
session_start();

header('Content-Type: application/json');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = mysqli_real_escape_string($conn, $_POST['userid']);
    $userLevel = (int)$_POST['userlevel'];

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

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $dob = mysqli_real_escape_string($conn, $_POST['DOB']);
    $phone = mysqli_real_escape_string($conn, $_POST['phono']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Update the user table
    $sql = "UPDATE $userType SET Username='$username', FullName='$fullname', Email='$email', DOB='$dob', PhoneNo='$phone', Password='$password' WHERE $userIdColumn='$userID'";
    if (!mysqli_query($conn, $sql)) {
        echo json_encode(['success' => false, 'message' => 'Error updating user: ' . mysqli_error($conn)]);
        exit;
    }

    // Update the logindetails table
    $sql = "UPDATE logindetails SET Username='$username', Password='$password' WHERE UserID='$userID' AND UserLevel='$userLevel'";
    if (!mysqli_query($conn, $sql)) {
        echo json_encode(['success' => false, 'message' => 'Error updating login details: ' . mysqli_error($conn)]);
        exit;
    }
    
    if ($_SESSION['UserID'] == $userID && $_SESSION['UserLevel'] == $userLevel) {
        $_SESSION['Username'] = $username;
    }
    echo json_encode(['success' => true]);
    mysqli_close($conn);
}
?>
