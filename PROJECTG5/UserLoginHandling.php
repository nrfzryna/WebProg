<?php
session_start(); // Start up your PHP Session

require_once("configuration.php");

$username = $_POST["username"];
$password = $_POST["password"];

$sql = "SELECT * FROM LoginDetails WHERE Username='$username' AND Password='$password'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $rows = mysqli_fetch_assoc($result);

    if ($rows) {
        $user_name = $rows["Username"];
        $user_id = $rows["UserID"];
        $user_level = $rows["UserLevel"];

        // Add user information to the session (global session variables)
        $_SESSION["Login"] = "YES";
        $_SESSION["Username"] = $user_name;
        $_SESSION["UserID"] = $user_id;
        $_SESSION["UserLevel"] = $user_level;

        // Determine the user type and fetch full name
        $userType = '';
        $userIdColumn = '';
        switch ($user_level) {
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

        // Fetch full name from the respective user table
        $fullNameSql = "SELECT FullName FROM $userType WHERE $userIdColumn = '$user_id'";
        $fullNameResult = mysqli_query($conn, $fullNameSql);
        if ($fullNameResult && mysqli_num_rows($fullNameResult) > 0) {
            $fullNameRow = mysqli_fetch_assoc($fullNameResult);
            $full_name = $fullNameRow['FullName'];
            $_SESSION["FullName"] = $full_name;
        } else {
            die("Error fetching full name.");
        }

        // Set a cookie to store user information
        setcookie("user_login", serialize($_SESSION), time() + (86400 * 30), "/");

        // Redirect based on user level
        if ($user_level == 1) {
            echo "<script>
                window.location.href = 'AdminDef.php';
                </script>";
        } elseif ($user_level == 2) {
            echo "<script>
                window.location.href = 'ManagerDef.php';
                </script>";
        } elseif ($user_level == 3) {
            echo "<script>
                window.location.href = 'StaffDef.php';
                </script>";
        } else {
            echo "<script>
                alert('Invalid user level. Please contact the administrator.');
                window.location.href = 'LoginForm.php';
                </script>";
        }
    } else {
        $_SESSION["Login"] = "NO";
        echo "<script>
            alert('Incorrect username or password.');
            window.location.href = 'LoginForm.php';
            </script>";
    }
} else {
    $_SESSION["Login"] = "NO";
    echo "<script>
        alert('Incorrect username or password.');
        window.location.href = 'LoginForm.php';
        </script>";
}

mysqli_close($conn);
?>
