<?php
require_once("configuration.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usertype = $_POST['usertype'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phoneno'];
    $dob = $_POST['dob'];
    $name = $_POST['name'];

    $idPrefix = '';

    switch ($usertype) {
        case 'admin':
            $idPrefix = 'A';
            break;
        case 'manager':
            $idPrefix = 'M';
            break;
        case 'staff':
            $idPrefix = 'S';
            break;
        default:
            // Handle the default case or error
            break;
    }

    // Generate a unique ID starting with the prefix and a random number
    $idValue = $idPrefix . '_' . rand(1000, 9999);

    // Determine the table name based on the user type
    $userLevel = 0;
    $idField = '';
    $table = '';

    switch ($usertype) {
        case 'admin':
            $table = 'AdminList';
            $userLevel = 1;
            $idField = 'AdminID';
            break;
        case 'manager':
            $table = 'ManagerList';
            $userLevel = 2;
            $idField = 'ManagerID';
            break;
        case 'staff':
            $table = 'StaffList';
            $userLevel = 3;
            $idField = 'StaffID';
            break;
        default:
            echo "Invalid user type";
            exit();
    }

    if ($userLevel > 0) {
        // Insert into LoginDetails table
        $sqlInsertLoginDetails = "INSERT INTO LoginDetails (UserID, Username, Password, UserLevel) VALUES (?, ?, ?,?)";
        $stmtLoginDetails = $conn->prepare($sqlInsertLoginDetails);
        $stmtLoginDetails->bind_param("ssss", $idValue, $username, $password, $userLevel);
        
        // Insert into the specific user table
        $sql = "INSERT INTO $table ($idField, Username, Password, Email, PhoneNo, DOB, FullName) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $idValue, $username, $password, $email, $phone, $dob, $name);

        // Execute all statements
        if ($stmtLoginDetails->execute() && $stmt->execute() ) {
            $message = "New user added successfully.You may close this tab";
        } else {
            $message = "Error: " . $stmt->error;
        }

        // Close all statements
        $stmtLoginDetails->close();
        $stmt->close();
        
    } else {
        echo "Invalid user type";
        exit();
    }
    // Close database connection
    mysqli_close($conn);

    // Redirect back to the add user page with the message
    header("Location: AddUser.php?message=" . urlencode($message));
    exit();
} else {
    $message = "Error: " . $stmt->error;
}
?>
