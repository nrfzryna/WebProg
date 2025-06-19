<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="applyleave.css">
    <title>Apply Leave Form</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set the min attribute to today's date
            let today = new Date().toISOString().split('T')[0];
            document.getElementById('startdate').setAttribute('min', today);
        });

        function validate() {
            if (document.leaveForm.fullname.value === "") {
                alert("Please provide your Name!");
                document.leaveForm.fullname.focus();
                return false;
            }
            if (document.leaveForm.startdate.value === "") {
                alert("Please provide your Date!");
                document.leaveForm.startdate.focus();
                return false;
            } else {
                let inputDate = document.leaveForm.startdate.value;
                let today = new Date().toISOString().split('T')[0];
                if (inputDate < today) {
                    alert("The start date cannot be in the past.");
                    document.leaveForm.startdate.focus();
                    return false;
                }
            }
            if (document.leaveForm.duration.value === "") {
                alert("Please provide your Duration!");
                document.leaveForm.duration.focus();
                return false;
            }
            if (document.leaveForm.explanation.value === "") {
                alert("Please provide your Explanation!");
                document.leaveForm.explanation.focus();
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<div class="container">
    <div id="header">
        <h2>Apply Leave Form</h2>
    </div>
    <?php
    session_start();
    $staffID = $_SESSION['UserID'];
    $fullName = $_SESSION['FullName'];
    ?>
    <div id="Forms">
        <form name="leaveForm" method="POST" action="LeaveApplyHandling.php" onsubmit="return validate();">
            <fieldset>
                <legend>Fill in The Form</legend>
                <p>
                    <label for="staffID">Staff ID :</label>
                    <input type="text" id="staffID" name="staffID" value="<?php echo $staffID; ?>" class="disabled" readonly>
                </p>
                <p>
                    <label for="fullname">Name :</label>
                    <input type="text" id="fullname" name="fullname" value="<?php echo $fullName; ?>" class="disabled" readonly>
                </p>
                <p>
                    <label for="startdate">Start Date :</label>
                    <input type="date" id="startdate" name="startdate">
                </p>
                <p>
                    <label for="duration">Duration :</label>
                    <input type="text" id="duration" name="duration">
                </p>
                <p>
                    <label for="dropdown">Reason for the leave :</label>
                    <select id="dropdown" name="reason">
                        <option value="Emergency Leave">Emergency leave</option>
                        <option value="Sick">Sick</option>
                        <option value="Medical Appointment">Medical appointment</option>
                        <option value="Vacation">Vacations</option>
                    </select>
                </p>
                <p>
                    <label for="explanation">Explanation :</label>
                    <input type="text" id="explanation" name="explanation">
                </p>
                <p>
                    <input type="hidden" id="leaveId" name="leaveId">
                    <input type="submit" value="Submit" id="submitBtn">
                </p>
            </fieldset>
        </form>
    </div>
</div>
</body>
</html>
