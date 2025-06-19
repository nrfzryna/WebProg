<?php
require_once("configuration.php");
session_start();

$userLevel = isset($_GET['userlevel']) ? (int)$_GET['userlevel'] : 0;

$userType = '';
$userIdColumn = '';
$title = '';
switch ($userLevel) {
    case 1:
        $userType = 'AdminList';
        $userIdColumn = 'AdminID';
        $title = 'Admin List';
        break;
    case 2:
        $userType = 'ManagerList';
        $userIdColumn = 'ManagerID';
        $title = 'Manager List';
        break;
    case 3:
        $userType = 'StaffList';
        $userIdColumn = 'StaffID';
        $title = 'Staff List';
        break;
    default:
        die("Invalid user level specified.");
}

$filteredUserData = [];
$noSearchQuery = false;

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT $userIdColumn AS ID, Username, FullName FROM $userType WHERE Username LIKE '%$searchQuery%' OR $userIdColumn LIKE '%$searchQuery%'";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $filteredUserData[] = $row;
    }
    mysqli_close($conn);
} else {
    $noSearchQuery = true;
    $sql = "SELECT $userIdColumn AS ID, Username, FullName FROM $userType";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $filteredUserData[] = $row;
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link rel="stylesheet" href="editacc.css">
</head>
<body>
    <div class="container">
        <div id="header">
   
            <form id="searchForm" method="GET">
                <input type="text" id="searchInput" name="search" placeholder="Search by ID or Username">
                <input type="hidden" name="userlevel" value="<?php echo htmlspecialchars($userLevel); ?>">
                <button type="submit">Search</button>
            </form>
            <p><?php echo $title; ?></p>
        </div>
        <div id="tablelist">
            <?php if (empty($filteredUserData)): ?>
                <p id="noSearchMessage">No users found matching the search criteria. <span>Click here to reset.</span></p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>ID</th>
                            <th>Username</th>
                            <th>FullName</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
// Display filtered user data
$rowNumber = 1;
foreach ($filteredUserData as $user) {
 
    // Link each user row to SelfAccEdit.php with the encoded user ID and user level as query parameters
    echo '<tr class="userRow" onclick="window.location.href=\'SelfAccEdit.php?userid=' . htmlspecialchars($user['ID']). '&userlevel=' . $userLevel . '\'">';
    echo '<td>' . $rowNumber++ . '.</td>';
    echo '<td>' . htmlspecialchars($user['ID']) . '</td>';
    echo '<td>' . htmlspecialchars($user['Username']) . '</td>';
    echo '<td>' . htmlspecialchars($user['FullName']) . '</td>';
    echo '</tr>';
}
?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div id="buttonContainer">
            <button onclick="window.location.href='manageuser.php'">Back to Main Edit Page</button>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const noSearchMessage = document.getElementById('noSearchMessage');
            if (noSearchMessage) {
                noSearchMessage.addEventListener('click', function () {
                    window.location.href = window.location.pathname + "?userlevel=<?php echo $userLevel; ?>"; 
                });
            }
        });
    </script>
</body>
</html>
