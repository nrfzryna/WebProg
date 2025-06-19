<?php
session_start();
require_once("configuration.php");

if (!isset($_SESSION['Login']) || $_SESSION['Login'] != 'YES') {
    header('Location: loginform.php');
    exit();
}

//cookie authorization
if ($_SESSION["UserLevel"] !== "3" && $_SESSION["UserLevel"] !== "1") {
    die("Unauthorized user.");
    exit();
}

$staffUsername = isset($_SESSION['Username']) && $_SESSION['UserLevel'] == 3 ? $_SESSION['Username'] : '';
$userLevel = $_SESSION['UserLevel'];
$userID = $_SESSION['UserID'];

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

// Fetch limited leave history (only 3 records) for the logged-in staff
$sqlLeaveHistory = "SELECT * FROM StaffLeaveHistoryList WHERE StaffID = '$userID' ORDER BY FIELD(Status, 'Pending', 'Rejected', 'Approved') LIMIT 3";
$resultLeaveHistory = mysqli_query($conn, $sqlLeaveHistory);

mysqli_close($conn);

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="staff.css">
    <title>DASHBOARD</title>
    <script>
        function openAddUserTab1() {
            window.open("LeaveApplyForm.php", "_blank");
        }
        function openSettings() {
            window.location.href = "SMEdit.php?userlevel=<?php echo $_SESSION['UserLevel']; ?>&userid=<?php echo $_SESSION['UserID']; ?>";
        }
        function openHistory() {
            window.location.href = "ViewOwnHistory.php?userlevel=<?php echo htmlspecialchars($_SESSION['UserLevel']); ?>&userid=<?php echo htmlspecialchars($_SESSION['UserID']); ?>";
        }
        function openReportList() {
            window.location.href = "ReportListStaff.php?userlevel=<?php echo htmlspecialchars($_SESSION['UserLevel']); ?>&userid=<?php echo htmlspecialchars($_SESSION['UserID']); ?>";
        }
        function openLogOut() {
            window.location.href = "logout.php";
        }
    </script>
</head>
<body>
    <div class="grid-container">
        <div id="sidebar"> 
            <a href="javascript:void(0)" onclick="openAddUserTab1()">Apply Leave Form</a>
            <br><br><br>
            <a href="javascript:void(0)" onclick="openHistory()">View Leave Application</a>
            <br><br><br>
            <a href="javascript:void(0)" onclick="openReportList()">View Leave Report</a>
            <br><br><br>
            <a href="javascript:void(0)" onclick="openSettings()">Settings</a>
            <br><br><br><br>
            <a href="logout.php" onclick="openLogOut()">Log Out</a>
            <br><br> <br><br><br>
        </div>
        <div id="header">
            <a href="javascript:void(0)" onclick="openSettings()">
                <img src="IMAGES/profile1.png">
            </a>
            <br><br>
            <?php echo htmlspecialchars($staffUsername); ?> <br><?php echo htmlspecialchars($_SESSION['UserID']); ?><hr>
        </div>
        <div id="header2">
            <img src="IMAGES/Welcome.png">
        </div>
        <div id="header3">
            Welcome Staff <b><?php echo htmlspecialchars($staffUsername); ?>!</b>
        </div>
        <div id="tablelist">
         <h3>Total Leave History: <?php echo mysqli_num_rows($resultLeaveHistory); ?></h3>
            <?php if (mysqli_num_rows($resultLeaveHistory) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>LeaveID</th>
                            <th>StaffID</th>
                            <th>Full Name</th>
                            <th>Date</th>
                            <th>Duration</th>
                            <th>Reason Type</th>
                            <th>Explanation</th>
                            <th>Status</th>
                            <th>Manager Name  In Charge</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $rowNumber = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($resultLeaveHistory)): ?>
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
                <button type="button" onclick="openHistory()">Show More</button>
            <?php else: ?>
                <p>No leave history found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
