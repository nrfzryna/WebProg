<?php
session_start();
require_once("configuration.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the logged-in user's Staff ID from the session
    $id = $_SESSION['UserID'];
    $fullname = $_SESSION['FullName'];
    $date = $_POST['startdate'];
    $duration = $_POST['duration'];
    $reason = $_POST['reason'];
    $explanation = $_POST['explanation'];
    
    // Check if the StaffID exists in the StaffList table (you might want to perform this check)

    // Check if the staff already has a pending leave application
    $sqlCheckPending = "SELECT * FROM LeaveHistoryList WHERE StaffID = '$id' AND Status = 'Pending'";
    $resultCheckPending = mysqli_query($conn, $sqlCheckPending);

    if (mysqli_num_rows($resultCheckPending) > 0) {
        // Staff already has a pending leave application
        echo "<script>alert('You already have a pending leave application. Please wait for it to be approved or rejected before applying for new leave.'); window.location.href = 'LeaveApplyForm.php';</script>";
        exit;
    }

    // Generate Leave ID
    $leaveId = generateLeaveId($conn);

    // Insert the new leave application
    $status = 'Pending';

    $sqlInsertStaffLeave = "INSERT INTO StaffLeaveHistoryList (LeaveID, StaffID, FullName, StartDate, Duration, ReasonType, Explanation, Status) 
                            VALUES ('$leaveId', '$id', '$fullname', '$date', '$duration', '$reason', '$explanation', '$status')";
    if (mysqli_query($conn, $sqlInsertStaffLeave)) {
        // Insert into LeaveHistoryList
        $sqlInsertAllLeave = "INSERT INTO LeaveHistoryList (LeaveID, StaffID, FullName, StartDate, Duration, ReasonType, Explanation, Status) 
                              VALUES ('$leaveId', '$id', '$fullname', '$date', '$duration', '$reason', '$explanation', '$status')";
        if (mysqli_query($conn, $sqlInsertAllLeave)) {
            // Display alert with Leave ID
            echo "<script>alert('Your leave form has been submitted. The Leave ID is: $leaveId'); window.location.href ='LeaveApplyForm.php';</script>";
        } else {
            echo "Error inserting data into LeaveHistoryList: " . mysqli_error($conn);
        }
    } else {
        echo "Error: " . $sqlInsertStaffLeave . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}

function generateLeaveId($conn) {
    $leaveId = 'L' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT); // Generates 'L' followed by 3 digits

    // Check if generated leave ID already exists in database
    $sqlCheckLeaveId = "SELECT LeaveID FROM LeaveHistoryList WHERE LeaveID = '$leaveId'";
    $resultCheckLeaveId = mysqli_query($conn, $sqlCheckLeaveId);

    // If leave ID already exists, recursively call the function to generate a new one
    if (mysqli_num_rows($resultCheckLeaveId) > 0) {
        return generateLeaveId($conn); // Recursive call
    }

    return $leaveId;
}
?>
