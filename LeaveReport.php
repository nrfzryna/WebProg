<?php
session_start();
require_once("configuration.php");

if (!isset($_SESSION['Login']) || $_SESSION['Login'] != 'YES') {
    header('Location: loginform.php');
    exit();
}

$userLevel = (int)$_SESSION['UserLevel'];

// Retrieve LeaveID from the URL query parameter
$leaveID = isset($_GET['leaveId']) ? mysqli_real_escape_string($conn, $_GET['leaveId']) : null;

if (!$leaveID) {
    die("Leave ID not provided.");
}

$sql = "
    SELECT 
        LeaveID,
        StaffID, 
        StaffName, 
        StartDate,
        Duration,
        ReasonType, 
        Status, 
        Explanation, 
        ManagerID,
        ManagerName 
    FROM 
        Generatereport 
    WHERE 
        LeaveID = '$leaveID' 
";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Leave application not found or unauthorized access.");
}

$row = mysqli_fetch_assoc($result);

// Calculate End Date based on Start Date and Duration
$startDate = new DateTime($row['StartDate']);
$duration = intval($row['Duration']);
$endDate = clone $startDate;
$endDate->add(new DateInterval("P{$duration}D"));
$endDateFormatted = $endDate->format('Y-m-d'); // Format the date as per your requirement

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LEAVE APPLICATION REPORT</title>
    <link rel="stylesheet" href="leavereport.css">
</head>
<body>
    <div class="container">
        <div id="header">
            <h3>Leave Application Report</h3>
            <hr>
            <p>YOUR LEAVE APPLY HAS BEEN <span class="<?php echo $row['Status'] === 'Approved' ? 'status-approved' : 'status-rejected'; ?>"><?php echo htmlspecialchars($row['Status']); ?></span>!</p>
        </div>
        <div class="details">
            <div><span>LeaveID:</span> <?php echo htmlspecialchars($row['LeaveID']); ?></div>
            <div><span>Staff ID:</span> <?php echo htmlspecialchars($row['StaffID']); ?></div>
            <div><span>Staff Name:</span> <?php echo htmlspecialchars($row['StaffName']); ?></div>
            <div><span>Reason:</span> <?php echo htmlspecialchars($row['ReasonType']); ?></div>
            <div><span>Start Date:</span> <?php echo htmlspecialchars($row['StartDate']); ?></div>
            <div><span>End Date:</span> <?php echo htmlspecialchars($endDateFormatted); ?></div>
            <div><span>Duration:</span> <?php echo htmlspecialchars($row['Duration']); ?> day(s)</div>
            <div><span>Status:</span> <span class="<?php echo $row['Status'] === 'Approved' ? 'status-approved' : 'status-rejected'; ?>"><?php echo htmlspecialchars($row['Status']); ?></span></div>
            <div><span>Explanation of Staff:</span> <?php echo htmlspecialchars($row['Explanation']); ?></div>
            <?php if ($userLevel == 1): ?>
            <div><span>Manager ID In Charge:</span> <?php echo htmlspecialchars($row['ManagerID']); ?></div>
            <div><span>Manager Name In Charge:</span> <?php echo htmlspecialchars($row['ManagerName']); ?></div>
            <?php else: ?>
            <div><span>Manager Name In Charge:</span> <?php echo htmlspecialchars($row['ManagerName']); ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
