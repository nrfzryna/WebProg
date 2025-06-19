<?php
session_start();
require_once("configuration.php");

// Check if the user is logged in
if (!isset($_SESSION['Login']) || $_SESSION['Login'] != 'YES') {
    header('Location: LoginForm.php');
    exit();
}

$userLevel = (int)$_SESSION['UserLevel'];
$userID = $_SESSION['UserID'];

// Check if the user level is valid (1 for Admin, 2 for Manager, 3 for Staff)
if ($userLevel !== 1 && $userLevel !== 3) {
    die("Unauthorized access.");
}

// Initialize variables based on user level
$userType = '';
$userIdColumn = '';
$title = 'Leave Apply List';

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
        die("Unauthorized access.");
}

// Initialize an empty array to store filtered user data
$filteredUserData = [];

// Check if a search query is submitted
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['search']);

    // Fetch data from the respective user table based on user type and search query
    $sql = "SELECT * FROM StaffLeaveHistoryList WHERE LeaveID = '$searchQuery' ORDER BY FIELD(Status, 'Pending', 'Rejected', 'Approved')";
} else {
    // Default query to fetch leave history for the logged-in user
    $sql = "SELECT * FROM StaffLeaveHistoryList WHERE StaffID = '$userID' ORDER BY FIELD(Status, 'Pending', 'Rejected', 'Approved')";
}

$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="editacc.css">
    <style>
        .date-cell {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="header">
            <form id="searchForm" method="GET">
            <input type="text" id="searchInput" name="search" placeholder="Search by Leave ID">
                <input type="hidden" name="userlevel" value="<?php echo htmlspecialchars($userLevel); ?>">
                <button type="submit">Search</button>
            </form>
            <p><?php echo $title; ?></p>
        </div>
        <div id="tablelist">
            <?php if (mysqli_num_rows($result) > 0): ?>
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
                            <th>Manager Name In Charge</th>
                        </tr>
                    </thead>
                    <tbody>
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
            <?php else: ?>
                <p>No leave history found.</p>
            <?php endif; ?>
        </div>
        <div id="buttonContainer">
            <button onclick="window.location.href='StaffDef.php'">Back to Dashboard</button>
        </div>
    </div>
</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>
