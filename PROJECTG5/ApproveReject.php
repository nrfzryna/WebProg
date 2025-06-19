<?php
require_once("configuration.php");
session_start(); // Start session if not already started

if (!isset($_SESSION['Login']) || $_SESSION['Login'] != 'YES') {
    header('Location: loginform.php');
    exit();
}

//cookie authorization
if ($_SESSION["UserLevel"] !== "2" && $_SESSION["UserLevel"] !== "1") {
    die("Unauthorized user.");
    exit();
}

$managerId = $_SESSION['UserID']; // Manager ID from session
$managerName = $_SESSION['FullName']; // Manager full name from session

// Initialize variables
$searchQuery = '';
$whereClause = "WHERE Status = 'Pending'"; // Default filter for pending leave applications

// Check if a search query is submitted
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['search']);

    // Determine if the search query is a StaffID or LeaveID
    if (strpos($searchQuery, 'L') === 0 && strlen($searchQuery) === 4) {
        // Search by LeaveID
        $whereClause .= " AND LeaveID = '$searchQuery'";
    } else {
        // Search by StaffID
        $whereClause .= " AND StaffID = '$searchQuery'";
    }
}

// Fetch pending leave applications with necessary details
$sql = "SELECT * FROM LeaveHistoryList $whereClause";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager View - Staff Leave Applications</title>
    <link rel="stylesheet" href="editacc.css">
</head>
<body>
<div class="container">
    <div id="header">
        <form id="searchForm" method="GET">
            <input type="text" id="searchInput" name="search" placeholder="Search by Staff ID or Leave ID" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>
        <p>Pending Staff Leave Applications</p>
    </div>

    <div id="tablelist">
        <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <tr>
            <th>No.</th>
                <th>LeaveID</th>
                <th>StaffID</th>
                <th>Full Name</th>
                <th>Date</th>
                <th>Duration</th>
                <th>Reason</th>
                <th>Explanation</th>
                <th>Action</th>
            </tr>
            <?php $rowNumber = 1; ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                <td><?php echo $rowNumber++; ?></td>
                    <td><?php echo htmlspecialchars($row['LeaveID']); ?></td>
                    <td><?php echo htmlspecialchars($row['StaffID']); ?></td>
                    <td><?php echo htmlspecialchars($row['FullName']); ?></td>
                    <td class="date-cell"><?php echo htmlspecialchars($row['StartDate']); ?></td>
                    <td><?php echo htmlspecialchars($row['Duration']); ?></td>
                    <td><?php echo htmlspecialchars($row['ReasonType']); ?></td>
                    <td><?php echo htmlspecialchars($row['Explanation']); ?></td>
                    <td class="date-cell">
                        <form method="POST" action="ApproveReject.php">
                            <input type="hidden" name="LeaveID" value="<?php echo htmlspecialchars($row['LeaveID']); ?>">
                            <input type="hidden" name="StaffID" value="<?php echo htmlspecialchars($row['StaffID']); ?>">
                            <input type="hidden" name="FullName" value="<?php echo htmlspecialchars($row['FullName']); ?>">
                            <label class="radio-label">
                                <input type="radio" name="action" value="approved" required> Approve
                            </label><br><br>
                            <label class="radio-label">
                                <input type="radio" name="action" value="rejected" required> Reject
                            </label><br><br>
                            <input type="submit" name="submit" value="Submit">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <p>No pending leave applications.</p>
        <?php endif; ?>
    </div>
    <div id="buttonContainer">
            <button onclick="window.location.href= 'ManagerDef.php'">Back to Dashboard</button>
        </div>
</div>

    <?php
    // Handle form submission to approve/reject leave applications
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
        // Retrieve form data
        $leaveId = $_POST['LeaveID'];
        $staffId = $_POST['StaffID'];
        $staffName = $_POST['FullName']; // Retrieve full name from hidden input
        $action = $_POST['action'];

        if ($action == 'approved' || $action == 'rejected') {
            $status = ucfirst($action); // Capitalize first letter to match 'Approved' or 'Rejected'

            // Fetch details from LeaveHistoryList
            $sqlFetchLeaveDetails = "SELECT * FROM LeaveHistoryList WHERE LeaveID = '$leaveId' AND Status = 'Pending'";
            $leaveResult = mysqli_query($conn, $sqlFetchLeaveDetails);
            $leaveData = mysqli_fetch_assoc($leaveResult);

            if ($leaveData) {
                // Extract leave details
                $startDate = $leaveData['StartDate'];
                $duration = $leaveData['Duration'];
                $reasonType = $leaveData['ReasonType'];
                $explanation = $leaveData['Explanation'];

                // Update LeaveHistoryList
                $sqlUpdateLeave = "UPDATE LeaveHistoryList SET Status = '$status' WHERE LeaveID = '$leaveId' AND Status = 'Pending'";
                $resultLeaveUpdate = mysqli_query($conn, $sqlUpdateLeave);
                if (!$resultLeaveUpdate) {
                    echo "Error updating LeaveHistoryList: " . mysqli_error($conn);
                }

                // Update StaffLeaveHistoryList
                $sqlUpdateStaffLeave = "UPDATE StaffLeaveHistoryList SET ManagerName = '$managerName', Status = '$status' WHERE LeaveID = '$leaveId' AND Status = 'Pending'";
                $resultStaffLeaveUpdate = mysqli_query($conn, $sqlUpdateStaffLeave);
                if (!$resultStaffLeaveUpdate) {
                    echo "Error updating StaffLeaveHistoryList: " . mysqli_error($conn);
                }

                // Insert into GenerateReport
                $sqlInsertReport = "INSERT INTO GenerateReport (LeaveID, StaffID, StaffName, StartDate, Duration, ReasonType, Explanation, Status, ManagerID, ManagerName) 
                                   VALUES ('$leaveId', '$staffId', '$staffName', '$startDate', '$duration', '$reasonType', '$explanation', '$status', '$managerId', '$managerName')";
                $resultReportInsert = mysqli_query($conn, $sqlInsertReport);
                if (!$resultReportInsert) {
                    echo "Error inserting into GenerateReport: " . mysqli_error($conn);
                }
            } else {
                echo "Error: No pending leave application found for LeaveID $leaveId.";
            }
        }
        // Redirect or refresh the page after processing
        header("Location: ApproveReject.php");
        exit();
    }

    // Close database connection
    mysqli_close($conn);
    ?>
</body>
</html>
