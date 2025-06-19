<?php
require_once("configuration.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phoneno'];
    $dob = $_POST['dob'];
    $table = $_POST['table'];

    // Check which table to insert into based on the username
    $userLevel = 0;
    $idField = '';
    $idValue = '';

    if ($table == 'AdminList') {
        $userLevel = 1;
        $idField = 'AdminID';
        $idValue = uniqid('A'); // Generate a unique ID starting with 'A'
    } elseif ($table == 'ManagerList') {
        $userLevel = 2;
        $idField = 'ManagerID';
        $idValue = uniqid('M'); // Generate a unique ID starting with 'M'
    } elseif ($table == 'StaffList') {
        $userLevel = 3;
        $idField = 'StaffID';
        $idValue = uniqid('S'); // Generate a unique ID starting with 'S'
    }

    if ($userLevel > 0) {
        // Insert into UserLogin table
        $sqlUserLogin = "INSERT INTO UserLogin (Username, Password, UserLevel) VALUES ('$username', '$password', '$userLevel')";
        if (mysqli_query($conn, $sqlUserLogin)) {
            echo "New UserLogin record created successfully<br>";
        } else {
            echo "Error: " . $sqlUserLogin . "<br>" . mysqli_error($conn);
        }

        // Insert into the specific list table
        $sql = "INSERT INTO $table ($idField, Username, Password, Email, PhoneNo, DOB) VALUES ('$idValue', '$username', '$password', '$email', '$phone', '$dob')";
        if (mysqli_query($conn, $sql)) {
            echo "New record created successfully in $table<br>";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        // Insert data into LoginDetails table
        $sqlInsertLoginDetails = "INSERT INTO LoginDetails (Username, Password, UserLevel) VALUES ('$username', '$password', '$userLevel')";
        if (mysqli_query($conn, $sqlInsertLoginDetails)) {
            echo "Data inserted successfully into LoginDetails<br>";
        } else {
            echo "Error inserting data into LoginDetails: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid username. The username must start with 'A' for admin, 'M' for manager, or 'S' for staff.";
    }

    mysqli_close($conn);
}
?>