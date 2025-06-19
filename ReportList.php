<?php
session_start();
require_once("configuration.php");

$userLevel = (int)$_SESSION['UserLevel'];

// Check if the user level is 1 (Admin) or 2 (Manager)
if ($userLevel !== 1 && $userLevel !== 2) {
    // Redirect to unauthorized page or display an error message
    die("Unauthorized access.");
}

// Initialize variables based on user level
$userType = '';
$userIdColumn = '';
$dashboardLink='';


// Set database table and column based on user level
switch ($userLevel) {
    case 1:
        $userType = 'AdminList';
        $userIdColumn = 'AdminID';
        $dashboardLink= 'AdminDef.php';
        break;
    case 2:
        $userType = 'ManagerList';
        $userIdColumn = 'ManagerID';
        $dashboardLink = 'ManagerDef.php';
        break;
    default:
        // Redirect to unauthorized page or display an error message
        die("Unauthorized access.");
}

// Initialize an empty array to store filtered user data
$filteredUserData = [];

// Check if a search query is submitted
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['search']);

    // Fetch data from Generatereport based on LeaveID and user type
    $sql = "SELECT * FROM Generatereport WHERE LeaveID = '$searchQuery'";
} else {
    // Default query without search filter
    $sql = "SELECT * FROM Generatereport ORDER BY FIELD(Status, 'Rejected', 'Approved')";
}

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORT LIST</title>
    <link rel="stylesheet" href="editacc.css">
  
</head>
<body>
    <div class="container">
        <div id="header">
            <form id="searchForm" method="GET">
                <input type="text" id="searchInput" name="search" placeholder="Search by Leave ID">
                <input type="hidden" name="userlevel" value="<?php echo htmlspecialchars($userLevel); ?>">
                <button type="submit">Search</button>
            </form>
            <p>Rejected and Approved Leave Lists</p>
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
                            <th>Start Date</th>
                            <th>Duration</th>
                            <th>Reason Type</th>
                            <th>Explanation</th>
                            <th>Status</th>
                            <?php if ($userLevel == 1): ?>
                                <th>Manager ID</th>
                                <th>Manager Name</th>
                            <?php else: ?>
                                <th>Manager Name</th>
                                <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $rowNumber = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                       
                        <tr class="userRow" data-leave-id="<?php echo htmlspecialchars($row['LeaveID']); ?>">
                            <td><?php echo $rowNumber++; ?></td>
                            <td><?php echo htmlspecialchars($row['LeaveID']); ?></td>
                            <td><?php echo htmlspecialchars($row['StaffID']); ?></td>
                            <td><?php echo htmlspecialchars($row['StaffName']); ?></td>
                            <td class="date-cell"><?php echo htmlspecialchars($row['StartDate']); ?></td>
                            <td><?php echo htmlspecialchars($row['Duration']); ?></td>
                            <td><?php echo htmlspecialchars($row['ReasonType']); ?></td>
                            <td><?php echo htmlspecialchars($row['Explanation']); ?></td>
                            <td class="<?php echo $row['Status'] === 'Approved' ? 'status-approved' : 'status-rejected'; ?>"><?php echo htmlspecialchars($row['Status']); ?></td>
                            <?php if ($userLevel == 1): ?>
                                <td><?php echo htmlspecialchars($row['ManagerID']); ?></td>
                                <td><?php echo htmlspecialchars($row['ManagerName']); ?></td>
                            <?php else: ?>
                                <td><?php echo htmlspecialchars($row['ManagerName']); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No leave history found.</p>
            <?php endif; ?>
        </div>
        <div id="buttonContainer">
            <button onclick="window.location.href='<?php echo $dashboardLink; ?>'">Back to Dashboard</button>
        </div>
    </div>

    <script>
        // Add click event listener to each row with class 'userRow'
        const rows = document.querySelectorAll('.userRow');
        rows.forEach(row => {
            row.addEventListener('click', function() {
                const leaveId = this.getAttribute('data-leave-id');
                window.open(`LeaveReport.php?leaveId=${leaveId}`, '_blank');
            });
        });
    </script>
</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>
