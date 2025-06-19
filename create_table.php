<?php
 
require_once ("configuration.php");
 
$sql1 = "CREATE TABLE AdminList(
    AdminID VARCHAR(6) NOT NULL PRIMARY KEY,
    Username VARCHAR(15) NOT NULL,
    Password VARCHAR(15) NOT NULL,
    FullName VARCHAR(50) NOT NULL,
    Email VARCHAR(50) NOT NULL,
    PhoneNo VARCHAR(15) NOT NULL, 
    DOB DATE NOT NULL)";

if (mysqli_query($conn, $sql1)) {
    echo "<br>Table AdminList created successfully";
} 
else {
    echo "Error creating table: " . mysqli_error($conn);
}

$sql2 = "CREATE TABLE ManagerList(
    ManagerID VARCHAR(6) NOT NULL PRIMARY KEY,
    Username VARCHAR(15) NOT NULL,
    Password VARCHAR(15) NOT NULL,
    FullName VARCHAR(50) NOT NULL,
    Email VARCHAR(50) NOT NULL,
    PhoneNo VARCHAR(15) NOT NULL, 
    DOB DATE NOT NULL)";

if (mysqli_query($conn, $sql2)) {
    echo "<br>Table ManagerList created successfully";
} 
else {
    echo "Error creating table: " . mysqli_error($conn);
}

$sql3 = "CREATE TABLE StaffList(
    StaffID VARCHAR(6) NOT NULL PRIMARY KEY,
    Username VARCHAR(15) NOT NULL,
    Password VARCHAR(15) NOT NULL,
    FullName VARCHAR(50) NOT NULL,
    Email VARCHAR(50) NOT NULL,
    PhoneNo VARCHAR(15) NOT NULL, 
    DOB DATE NOT NULL)";

if (mysqli_query($conn, $sql3)) {
    echo "<br>Table StaffList created successfully";
} 
else {
    echo "Error creating table: " . mysqli_error($conn);
}

$sql4 = "CREATE TABLE LeaveHistoryList(
    StaffID VARCHAR(6) NOT NULL,
    LeaveID VARCHAR(6) NOT NULL,
    FullName VARCHAR(50) NOT NULL,
    StartDate DATE NOT NULL,
    Duration INT(2) NOT NULL,
    ReasonType VARCHAR(30) NOT NULL,
    Explanation VARCHAR(100) NOT NULL,
    Status VARCHAR(10) NOT NULL)";

if (mysqli_query($conn, $sql4)) {
    echo "<br>Table LeaveHistoryList created successfully";
} 
else {
    echo "Error creating table: " . mysqli_error($conn);
}

$sql5 = "CREATE TABLE StaffLeaveHistoryList(
    StaffID VARCHAR(6) NOT NULL,
    LeaveID VARCHAR(6) NOT NULL,
    FullName VARCHAR(50) NOT NULL,
    StartDate DATE NOT NULL,
    Duration INT(2) NOT NULL,
    ReasonType VARCHAR(30) NOT NULL,
    Explanation VARCHAR(100) NOT NULL,
    ManagerName VARCHAR(50) NOT NULL,
    Status VARCHAR(10) NOT NULL)";


if (mysqli_query($conn, $sql5)) {
    echo "<br>Table StaffLeaveHistoryList created successfully";
} 
else {
    echo "Error creating table: " . mysqli_error($conn);
}

$sqlJoinLogin = "CREATE TABLE LoginDetails (
    UserID VARCHAR(6) NOT NULL,
    Username VARCHAR(15) NOT NULL,
    Password VARCHAR(15) NOT NULL,
    UserLevel INT(2) NOT NULL)";

if (mysqli_query($conn, $sqlJoinLogin)) {
    echo "<br>Table LoginDetails created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

$sqlGeneratereport = "CREATE TABLE Generatereport (
    LeaveID VARCHAR(6) NOT NULL,
    StaffID VARCHAR(6) NOT NULL,
    StaffName VARCHAR(50) NOT NULL,
    StartDate DATE NOT NULL,
    Duration INT(2) NOT NULL,
    ReasonType VARCHAR(30) NOT NULL,
    Explanation VARCHAR(1000) NOT NULL,
    Status VARCHAR(10) NOT NULL,
    ManagerID VARCHAR(6) NOT NULL,
    ManagerName VARCHAR(50) NOT NULL)";
    
if (mysqli_query($conn, $sqlGeneratereport)) {
    echo "<br>Table Generate Report created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>