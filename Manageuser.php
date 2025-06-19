
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
?>
<!DOCTYPE html>
<html>
<head>
    <title>SELECT ACCOUNT TO EDIT</title>
    <link rel="stylesheet" href="manageuser.css">
</head>
<body>
    <br><br><br><br><br><br><br><br>
    <h1>Which Account would you like to Edit ?</h1>
    <div class="Userselection">
        <div id="Admintype">
            <!-- Pass '1' as a parameter in the URL for admin -->
            <a href="editacc.php?userlevel=1"> 
                <img src="IMAGES/Admin.png" alt="Admin"> 
            </a>
            <p>Admin</p>
        </div>
        <div id="Managertype">
            <!-- Pass '2' as a parameter in the URL for manager -->
            <a href="editacc.php?userlevel=2"> 
                <img src="IMAGES/Manager.png" alt="Manager"> 
            </a>
            <p>Manager</p>
        </div>
        <div id="Stafftype">
            <!-- Pass '3' as a parameter in the URL for staff -->
            <a href="editacc.php?userlevel=3"> 
                <img src="IMAGES/Staff.png" alt="Staff"> 
            </a>
            <p>Staff</p>
        </div>
        <div id="buttonContainer">
        <button onclick="window.location.href='AdminDef.php'">Back to Dashboard</button>
        </div>
    </div>
</body>
</html>
