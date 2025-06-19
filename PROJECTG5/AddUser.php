<?php
session_start();
require_once("configuration.php");
if (!isset($_SESSION['Login']) || $_SESSION['Login'] != 'YES') {
    header('Location: loginform.php');
    exit();
}

//cookie authorization
if ($_SESSION["UserLevel"] !== "1") {
    die("Unauthorized user.");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="AddUser.css">
    <title>Add New User</title>
    <script>
        function clearMessage() {
            // Check if URL contains the 'message' parameter
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('message')) {
                // Display the message
                alert(urlParams.get('message'));

                // Remove 'message' parameter from URL without reloading the page
                const url = new URL(window.location);
                url.searchParams.delete('message');
                window.history.replaceState({}, document.title, url);
            }
        }
    </script>
</head>
<body onload="clearMessage()">
    <div class="grid-container">
        <div id="Forms">
            <h1>Add New User</h1>
            <form id="userForm" action="NewUserHandling.php" method="POST" onsubmit="return validateForm()">
                <label for="usertype">User Type:</label>
                <select id="usertype" name="usertype" onchange="setTableName()" required>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="manager">Manager</option>
                </select>
                <br><br>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <br><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <br><br>
                <label for="phoneno">Phone No:</label>
                <input type="tel" id="phoneno" name="phoneno" value="0102345678" required>
                <br><br>
                <label for="dob">Birthday:</label>
                <input type="date" id="dob" name="dob" required>
                <br><br>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <p>Username must equal or less than 15 characters and <br>start with 'A' for Admin, 'S' for Staff, 'M' for Manager.</p>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <p>Password must contain one special character and start <br>with a letter, and be at least 6 characters long.</p>
                <br>
                <input type="submit" value="Add User">
                <input type="hidden" name="table">
            </form>
        </div>
    </div>
    <script>
        function setTableName() {
            var userType = document.getElementById("usertype").value;
            var tableField = document.getElementById("table");
            if (userType === "admin") {
                tableField.value = "AdminList";
            } else if (userType === "staff") {
                tableField.value = "StaffList";
            } else if (userType === "manager") {
                tableField.value = "ManagerList";
            }
        }

        function validateEmail(email) {
            var atIndex = email.indexOf("@");
            var lastDotIndex = email.lastIndexOf(".");
            return atIndex > 0 && lastDotIndex > atIndex + 1 && lastDotIndex < email.length - 1;
        }

        function validateForm() {
            var userType = document.getElementById("usertype").value;
            var name = document.getElementById("name").value;
            var email = document.getElementById("email").value;
            var phone = document.getElementById("phoneno").value;
            var birthday = document.getElementById("dob").value;
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;
            var passwordRegex = /^(?=.*[A-Za-z])(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/;

            // Check if any field is empty
            if (userType === "" || name === "" || email === "" || phone === "" || birthday === "" || username === "" || password === "") {
                alert("Please fill in all fields.");
                return false;
            }

            // Validation for password
            if (!passwordRegex.test(password)) {
                alert("Password must contain one special character and start with a letter, and be at least 6 characters long.");
                return false;
            }

            // Validation for username length and prefix based on user type
            if (username.length > 15|| (userType === "admin" && username[0] !== 'A') ||
                (userType === "staff" && username[0] !== 'S') ||
                (userType === "manager" && username[0] !== 'M')) {
                alert("Username must be less than or equal to 15 characters long and start with 'A' for Admin, 'S' for Staff, 'M' for Manager.");
                return false;
            }
            if (!/^\d{10,11}$/.test(phone)) {
                alert("Please enter a valid Malaysia phone number.");
                return false;
            }
            // Validation for email format
            if (!validateEmail(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            // Confirm before submitting
            if (confirm("Confirm to add user?")) {
                return true; // Allow form submission
            } else {
                return false; // Prevent form submission if not confirmed
            }
        }
    </script>
</body>
</html>
