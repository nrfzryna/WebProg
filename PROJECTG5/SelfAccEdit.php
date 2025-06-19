<?php
require_once("configuration.php");
session_start();

if (!isset($_SESSION['Login']) || $_SESSION['Login'] != 'YES') {
    header('Location: loginform.php');
    exit();
}

// Check user authorization
if ($_SESSION["UserLevel"] !== "1") {
    die("Unauthorized user.");
    exit();
}

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
        $dashboardlink = 'AdminDef.php';
        break;
    case 3:
        $userType = 'StaffList';
        $userIdColumn = 'StaffID';
        $dashboardlink = 'AdminDef.php';
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
    <link rel="stylesheet" href="Selfaccedit.css">
    <script>
        function validateEmail(email) {
            var atIndex = email.indexOf("@");
            var lastDotIndex = email.lastIndexOf(".");
            return atIndex > 0 && lastDotIndex > atIndex + 1 && lastDotIndex < email.length - 1;
        }

        function validateForm() {
            var userType = "<?php echo $userType; ?>";
            var name = document.getElementById("fullname").value;
            var email = document.getElementById("email").value;
            var phone = document.getElementById("phono").value;
            var birthday = document.getElementById("DOB").value;
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;
            var passwordRegex = /^(?=.*[A-Za-z])(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/;

            if (name === "" || email === "" || phone === "" || birthday === "" || username === "" || password === "") {
                alert("Please fill in all fields.");
                return false;
            }

            if (!passwordRegex.test(password)) {
                alert("Password must contain one special character and start with a letter, and be at least 6 characters long.");
                return false;
            }

            if (username.length > 15 || (userType === "AdminList" && username[0] !== 'A') ||
                (userType === "ManagerList" && username[0] !== 'M') ||
                (userType === "StaffList" && username[0] !== 'S')) {
                alert("Username must be less or equal to 15 characters long and start with 'A' for Admin, 'S' for Staff, 'M' for Manager.");
                return false;
            }

            if (!/^\d{10,11}$/.test(phone)) {
                alert("Please enter a valid Malaysia phone number.");
                return false;
            }

            if (!validateEmail(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            return true;
        }

        document.addEventListener("DOMContentLoaded", function () {
            const editButton = document.getElementById('editButton');
            const updateButton = document.getElementById('updateButton');
            const deleteButton = document.getElementById('deleteButton');
            const formInputs = document.querySelectorAll('#userForm input');

            editButton.addEventListener('click', function () {
                formInputs.forEach(function (input) {
                    if (input.type !== 'hidden') {
                        input.removeAttribute('readonly');
                        input.classList.remove('disabled');
                    }
                });
                editButton.style.display = 'none';
                deleteButton.style.display = 'none';
                updateButton.style.display = 'inline';
            });

            deleteButton.addEventListener('click', function () {
                if (confirm("Are you sure you want to delete this account?")) {
                    const userId = "<?php echo htmlspecialchars($userID); ?>";
                    const userLevel = "<?php echo htmlspecialchars($userLevel); ?>";
                    deleteUser(userId, userLevel);
                }
            });

            document.getElementById('userForm').addEventListener('submit', function (event) {
                if (!validateForm()) {
                    event.preventDefault();
                } else {
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
                            window.location.href = `SelfAccEdit.php?userid=${userId}&userlevel=${userLevel}`;
                        } else {
                            alert('Error saving changes: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error saving changes');
                    });
                }
            });
        });

        function deleteUser(userId, userLevel) {
            fetch('delete_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ userid: userId, userlevel: userLevel }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Account deleted successfully');
                    window.location.href = '<?php echo $dashboardlink; ?>';
                } else {
                    alert('Error deleting account: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting account');
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <div id="userimg">
            <img src="IMAGES/profile1.png">
            <p>Account Information</p>
             <b>User ID: <?php echo htmlspecialchars($userID);?></b>
        </div>
        <div id="Info">
            <form id="userForm" method="post">
                <input type="hidden" name="userlevel" value="<?php echo htmlspecialchars($userLevel); ?>">
                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userID); ?>">

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

            <button type="button" id="deleteButton">Delete Account</button>
        </div>
        <div id="buttonContainer">
            <button onclick="window.location.href='<?php echo $dashboardlink; ?>'">Back to Dashboard</button>
        </div>
    </div>
</body>
</html>
