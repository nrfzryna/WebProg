
<?php
session_start();
require_once("configuration.php");

if (!isset($_SESSION['Login']) || $_SESSION['Login'] != 'YES') {
    header('Location: loginform.php');
    exit();
}

// Check user authorization
if ($_SESSION["UserLevel"] !== "1") {
    die("Unauthorized user.");
    exit();
}

$userLevel = $_SESSION['UserLevel'];
$userID = $_SESSION['UserID'];
$adminUsername = isset($_SESSION['Username']) && $_SESSION['UserLevel'] == 1 ? $_SESSION['Username'] : '';

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

// Retrieve counts of different user types
$sqlAdminCount = "SELECT COUNT(*) AS total_admins FROM AdminList";
$resultAdminCount = mysqli_query($conn, $sqlAdminCount);
$adminCount = mysqli_fetch_assoc($resultAdminCount)['total_admins'];

$sqlManagerCount = "SELECT COUNT(*) AS total_managers FROM ManagerList";
$resultManagerCount = mysqli_query($conn, $sqlManagerCount);
$managerCount = mysqli_fetch_assoc($resultManagerCount)['total_managers'];

$sqlStaffCount = "SELECT COUNT(*) AS total_staff FROM StaffList";
$resultStaffCount = mysqli_query($conn, $sqlStaffCount);
$staffCount = mysqli_fetch_assoc($resultStaffCount)['total_staff'];

// Retrieve the total number of leave applications for the current month
$currentMonth = date('Y-m');
$sqlLeaveCount = "SELECT COUNT(*) AS total_leave FROM LeaveHistoryList WHERE DATE_FORMAT(StartDate, '%Y-%m') = '$currentMonth'";
$resultLeaveCount = mysqli_query($conn, $sqlLeaveCount);
$leaveCount = mysqli_fetch_assoc($resultLeaveCount)['total_leave'];

// Check total number of leave applications
$sqlTotalLeaves = "SELECT COUNT(*) AS total FROM LeaveHistoryList";
$resultTotalLeaves = mysqli_query($conn, $sqlTotalLeaves);
$totalLeaves = mysqli_fetch_assoc($resultTotalLeaves)['total'];// Retrieve leave history with manager details
$leaveHistorySql = "SELECT lh.*, gr.ManagerID, gr.ManagerName 
                    FROM LeaveHistoryList lh
                    LEFT JOIN Generatereport gr ON lh.LeaveID = gr.LeaveID
                    WHERE lh.Status IN ('Pending', 'Rejected', 'Approved')
                    ORDER BY FIELD(lh.Status, 'Pending', 'Rejected', 'Approved')
                    LIMIT 3";
$leaveHistoryResult = mysqli_query($conn, $leaveHistorySql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admins.css">
    <title>DASHBOARD</title>
    <script>
        function openAddUserTab1() {
            window.open("AddUser.php", "_blank");
        }
        function openAddUserTab2() {
            window.location.href="Manageuser.php";
        }
        function openViewLeave() {
            window.location.href="ViewLeaveAll.php?userlevel=<?php echo $_SESSION['UserLevel']; ?>&userid=<?php echo $_SESSION['UserID']; ?>";
        }
        function openSettings() {
            window.location.href = "SelfAccEdit.php?userlevel=<?php echo $_SESSION['UserLevel']; ?>&userid=<?php echo $_SESSION['UserID']; ?>";
        }
        function openReport() {
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
            <a href="javascript:void(0)" onclick="openAddUserTab1()">Add New User</a>
            <br><br><br>
            <a href="javascript:void(0)" onclick="openAddUserTab2()">Manage User Account</a>
            <br><br><br>
            <a href="javascript:void(0)" onclick="openViewLeave()">View Leave Application</a>
            <br><br><br>
            <a href="javascript:void(0)" onclick="openReport()">View Leave Report</a>
            <br><br><br>
            <a href="javascript:void(0)" onclick="openSettings()">Settings</a>
            <br><br><br>
            <a href="javascript:void(0)" onclick="openLogOut()">Log Out</a>
        </div>
        <div id="header">
            <a href="javascript:void(0)" onclick="openSettings()">
                <img src="IMAGES/profile1.png">
            </a>
            <br><br>
            <?php echo htmlspecialchars($adminUsername); ?> <br><?php echo htmlspecialchars($_SESSION['UserID']); ?><hr>
        </div>
        <div id="header2">
            <img src="IMAGES/Welcome.png">
        </div>
        <div id="header3">
            Welcome Admin <b><?php echo htmlspecialchars($adminUsername); ?>!</b>
        </div>
        <div id="TotalAdmin">
            <h3>Total Admin Account(s):<hr><span class="count-number"><?php echo $adminCount; ?></span></h3>
        </div>
        <div id="TotalManager">
            <h3>Total Manager Account(s):<hr><span class="count-number"><?php echo $managerCount; ?></span></h3>
        </div>
        <div id="TotalStaff">
            <h3>Total Staff Account(s):<hr><span class="count-number"><?php echo $staffCount; ?></span></h3>
        </div>
        <div id="TotalLeave">
            <img src="IMAGES/TotalLeave.png">
            <h3>Total Leave Applications This Month:<hr><span class="count-number"><?php echo $leaveCount; ?></span></h3>
        </div>
        <div id="tablelist">
            <?php if (mysqli_num_rows($leaveHistoryResult) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Leave ID</th>
                            <th>Staff ID</th>
                            <th>Full Name</th>
                            <th>Date</th>
                            <th>Duration</th>
                            <th>Reason Type</th>
                            <th>Explanation</th>
                            <th>Status</th>
                            <th>Manager ID</th><th>Manager Name</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = mysqli_fetch_assoc($leaveHistoryResult)): ?>
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
                            <td><?php echo htmlspecialchars($row['ManagerID']); ?></td>
                            <td><?php echo htmlspecialchars($row['ManagerName']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <br>
            <?php else: ?>
                <p>No leave history found.</p>
            <?php endif; ?>
            <?php if ($totalLeaves > 3): ?>
                <button type="button" onclick="openViewLeave()">Show More</button>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
// Close database connection
mysqli_close($conn);
?>