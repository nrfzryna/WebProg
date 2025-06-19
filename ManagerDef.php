<?php
session_start();
require_once("configuration.php");

if (!isset($_SESSION['Login']) || $_SESSION['Login'] != 'YES') {
    header('Location: loginform.php');
    exit();
}

//cookie authorization
if ($_SESSION["UserLevel"] !== "2" && $_SESSION["UserLevel"] !== "1") {
    die("Unauthorized user.");
    exit();
}

$ManagerUsername = isset($_SESSION['Username']) && $_SESSION['UserLevel'] == 2 ? $_SESSION['Username'] : '';
$userLevel = $_SESSION['UserLevel'];
$userID = $_SESSION['UserID'];
$full_name = $_SESSION['FullName']; 

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
        die("Invalid user level specified.");
}

$sql = "SELECT * FROM $userType WHERE $userIdColumn = '$userID'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $userData = mysqli_fetch_assoc($result);

    // Update session variables if necessary
    $_SESSION['Username'] = $userData['Username'];
    // Update other session variables as needed
} else {
    die("User not found."); // This message should only show if the query fails or no rows are returned
}

$pendingLeaveSQL = "SELECT * FROM LeaveHistoryList WHERE Status = 'Pending' ORDER BY StartDate LIMIT 3";
$pendingLeaveResult = mysqli_query($conn, $pendingLeaveSQL);

// Count total pending leave applications
$totalPendingLeaveSQL = "SELECT COUNT(*) AS total_pending FROM LeaveHistoryList WHERE Status = 'Pending'";
$totalPendingLeaveResult = mysqli_query($conn, $totalPendingLeaveSQL);
$totalPendingLeaves = mysqli_fetch_assoc($totalPendingLeaveResult)['total_pending'];

// Select up to 3 Approved or Rejected leave applications combined, with ManagerName
$approvedRejectedLeaveSQL = "
    SELECT lh.*, gr.ManagerName
    FROM LeaveHistoryList lh
    LEFT JOIN Generatereport gr ON lh.LeaveID = gr.LeaveID
    WHERE lh.Status IN ('Approved', 'Rejected')
    ORDER BY CASE
               WHEN lh.Status = 'Rejected' THEN 1
               WHEN lh.Status = 'Approved' THEN 2
               ELSE 3
             END,
             lh.StartDate DESC
    LIMIT 3
";

$approvedRejectedLeaveResult = mysqli_query($conn, $approvedRejectedLeaveSQL);

// Count total approved leave applications
$totalApprovedLeaveSQL = "SELECT COUNT(*) AS total_approved FROM LeaveHistoryList WHERE Status = 'Approved'";
$totalApprovedLeaveResult = mysqli_query($conn, $totalApprovedLeaveSQL);
$totalApprovedLeaves = mysqli_fetch_assoc($totalApprovedLeaveResult)['total_approved'];

// Count total rejected leave applications
$totalRejectedLeaveSQL = "SELECT COUNT(*) AS total_rejected FROM LeaveHistoryList WHERE Status = 'Rejected'";
$totalRejectedLeaveResult = mysqli_query($conn, $totalRejectedLeaveSQL);
$totalRejectedLeaves = mysqli_fetch_assoc($totalRejectedLeaveResult)['total_rejected'];

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="manager.css">
    <title>Manager Dashboard</title>
    <script>
        function openSettings() {
            window.location.href = "SMEdit.php?userlevel=<?php echo $_SESSION['UserLevel']; ?>&userid=<?php echo $_SESSION['UserID']; ?>";
        }
        function openEditLeave() {
            window.location.href = "ApproveReject.php?userlevel=<?php echo $_SESSION['UserLevel']; ?>&userid=<?php echo $_SESSION['UserID']; ?>";
        }
        function openLeaveList() {
            window.location.href = "ViewLeaveAll.php?userlevel=<?php echo $_SESSION['UserLevel']; ?>&userid=<?php echo $_SESSION['UserID']; ?>";
        }
        function openLeaveReport() {
            window.location.href = "ReportList.php?userlevel=<?php echo $_SESSION['UserLevel']; ?>&userid=<?php echo $_SESSION['UserID']; ?>";
        }
        function openLogOut() {
            window.location.href = "logout.php";
        }
    </script>
</head>
<body>
    <div class="grid-container">
        <div id="sidebar"> 
            <a href="javascript:void(0)" onclick="openEditLeave()">Approve/Reject Leave</a>
            <br><br><br>
            <a href="javascript:void(0)" onclick="openLeaveList()">View Leave Applications</a>
            <br><br><br>
            <a href="javascript:void(0)" onclick="openLeaveReport()">View Leave Reports</a>
            <br><br><br>
            <a href="javascript:void(0)" onclick="openSettings()">Settings</a>
            <br><br><br>
            <a href="logout.php" onclick="openLogOut()">Log Out</a>
        </div>
        <div id="header">
            <a href="javascript:void(0)" onclick="openSettings()">
                <img src="IMAGES/profile1.png">
            </a>
            <br><br>
            <?php echo htmlspecialchars($ManagerUsername); ?> <br><?php echo htmlspecialchars($_SESSION['UserID']); ?><hr>
        </div>
        <div id="header2">
            <img src="IMAGES/Welcome.png">
        </div>
        <div id="header3">
            Welcome Manager <b><?php echo htmlspecialchars($ManagerUsername); ?>!</b>
        </div>
        <div class="grid-item" id="total-pending">
        <h3>Total Pending Leave Applications:<hr><span class="count-number"><?php echo $totalPendingLeaves; ?></span></h3>
            
        </div>

        <div class="grid-item" id="total-approved">
        <h3>Total Approved Leave Applications:<hr><span class="count-number"><?php echo $totalApprovedLeaves; ?></span></h3>
            
        </div>

        <div class="grid-item" id="total-rejected">
        <h3>Total Rejected Leave Applications:<hr><span class="count-number"><?php echo $totalRejectedLeaves; ?></span></h3>
            
        </div>

        <div id="tablelist">
            <h2>Pending Leave Applications</h2>
            <?php if (mysqli_num_rows($pendingLeaveResult) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Leave ID</th>
                            <th>Staff ID</th>
                            <th>Staff Name</th>
                            <th>Start Date</th>
                            <th>Duration</th>
                            <th>Reason Type</th>
                            <th>Explanation</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($pendingLeaveResult)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['LeaveID']); ?></td>
                                <td><?php echo htmlspecialchars($row['StaffID']); ?></td>
                                <td><?php echo htmlspecialchars($row['FullName']); ?></td>
                                <td class="date-cell"><?php echo htmlspecialchars($row['StartDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['Duration']); ?></td>
                                <td><?php echo htmlspecialchars($row['ReasonType']); ?></td>
                                <td><?php echo htmlspecialchars($row['Explanation']); ?></td>
                                <td class="<?php
                                    if ($row['Status'] === 'Approved') {
                                        echo 'status-approved';
                                    } elseif ($row['Status'] === 'Rejected') {
                                        echo 'status-rejected';
                                    } elseif ($row['Status'] === 'Pending') {
                                        echo 'status-pending';
                                    }
                                ?>"><?php echo htmlspecialchars($row['Status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <br>
                <button type="button" onclick="openEditLeave()">Show More</button>
            <?php else: ?>
                <p>No pending leave applications.</p>
            <?php endif; ?>
        </div>

        <div id="tabletotal">
            <h2>Approved & Rejected Leave Applications</h2>
            <?php if (mysqli_num_rows($approvedRejectedLeaveResult) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Leave ID</th>
                            <th>Staff ID</th>
                            <th>Staff Name</th>
                            <th>Start Date</th>
                            <th>Duration</th>
                            <th>Reason Type</th>
                            <th>Explanation</th>
                            <th>Status</th>
                            <th>Manager Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rowNumber = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($approvedRejectedLeaveResult)): ?>
                            <tr>
                                <td><?php echo $rowNumber++; ?></td>
                                <td><?php echo htmlspecialchars($row['LeaveID']); ?></td>
                                <td><?php echo htmlspecialchars($row['StaffID']); ?></td>
                                <td><?php echo htmlspecialchars($row['FullName']); ?></td>
                                <td class="date-cell"><?php echo htmlspecialchars($row['StartDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['Duration']); ?></td>
                                <td><?php echo htmlspecialchars($row['ReasonType']); ?></td>
                                <td><?php echo htmlspecialchars($row['Explanation']); ?></td>
                                <td class="<?php
                                    if ($row['Status'] === 'Approved') {
                                        echo 'status-approved';
                                    } elseif ($row['Status'] === 'Rejected') {
                                        echo 'status-rejected';
                                    } elseif ($row['Status'] === 'Pending') {
                                        echo 'status-pending';
                                    }
                                ?>"><?php echo htmlspecialchars($row['Status']); ?></td>
                                <td><?php echo htmlspecialchars($row['ManagerName']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <br>
                <button type="button" onclick="openLeaveReport()">Show More</button>
            <?php else: ?>
                <p>No approved or rejected leave applications.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>