<?php
require_once("configuration.php");
session_start();

$userLevel = isset($_GET['userlevel']) ? (int)$_GET['userlevel'] : 0;
$userID = mysqli_real_escape_string($conn, $_GET['userid']);

$userType = '';
$userIdColumn = '';
$dashboardlink = '';

switch ($userLevel) {
    case 1:
        $userType = 'AdminList';
        $userIdColumn = 'AdminID';
        $dashboardlink = 'AdminDef.php';
        break;
    case 2:
        $userType = 'ManagerList';
        $userIdColumn = 'ManagerID';
        $dashboardlink = 'ManagerDef.php';
        break;
    case 3:
        $userType = 'StaffList';
        $userIdColumn = 'StaffID';
        $dashboardlink = 'StaffDef.php';
        break;
    default:
        die("Invalid user level specified.");
}

$sql = "SELECT * FROM $userType WHERE $userIdColumn = '$userID'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $userData = mysqli_fetch_assoc($result);
} else {
    die("User not found.");
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
    <link rel="stylesheet" href="selfaccedit.css">
    <style>
    </style>
</head>
<body>
    <div class="container">
        <div id="userimg">
            <img src="IMAGES/profile1.png">
            <p>Account Information</p>
            User ID: <?php echo htmlspecialchars($_SESSION['UserID']); ?>
        </div>
        <div id="Info">
            <form id="userForm">
                <input type="hidden" name="userlevel" value="<?php echo htmlspecialchars($userLevel); ?>">
                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userData[$userIdColumn]); ?>">

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['Username']); ?>" required class="disabled" readonly>
                <br>
                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($userData['FullName']); ?>" required class="disabled" readonly>
                <br>
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($userData['Email']); ?>" required class="disabled" readonly>
                <br>
                <label for="DOB">Birthday:</label>
                <input type="date" id="DOB" name="DOB" value="<?php echo htmlspecialchars($userData['DOB']); ?>" required class="disabled" readonly>
                <br>
                <label for="phono">Phone No:</label>
                <input type="text" id="phono" name="phono" value="<?php echo htmlspecialchars($userData['PhoneNo']); ?>" required class="disabled" readonly>
                <br>
                <label for="password">Password:</label>
                <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($userData['Password']); ?>" required class="disabled" readonly>
                <br>
                <button type="button" id="editButton">Edit Account</button>
                <button type="submit" id="updateButton" style="display:none;">Save Changes</button>
            </form>
        </div>
        <div id="buttonContainer">
        <button onclick="window.location.href='<?php echo $dashboardlink; ?>'">Back to Dashboard</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const editButton = document.getElementById('editButton');
            const updateButton = document.getElementById('updateButton');
            const formInputs = document.querySelectorAll('#userForm input');

            editButton.addEventListener('click', function () {
                formInputs.forEach(function (input) {
                    if (input.type !== 'hidden') {
                        input.removeAttribute('readonly');
                        input.classList.remove('disabled');
                    }
                });
                editButton.style.display = 'none';
                updateButton.style.display = 'inline';
            });

            document.getElementById('userForm').addEventListener('submit', function (event) {
                event.preventDefault();
                const formData = new FormData(this);

                fetch('update_user.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Changes saved');
                        const userId = "<?php echo htmlspecialchars($userID); ?>";
                        const userLevel = "<?php echo htmlspecialchars($userLevel); ?>";
                        window.location.href = `SMEdit.php?userid=${userId}&userlevel=${userLevel}`;
                    } else {
                        alert('Error saving changes: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving changes');
                });
            });
        });
    </script>

</body>
</html>
