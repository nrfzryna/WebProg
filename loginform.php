<!-- Semua user login  -->
<html> 
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Welcome to Login</title> 
    <script>
        function validateUser() {
            let username = document.forms["loginform"]["username"].value;
            let password = document.forms["loginform"]["password"].value;
            let valid = true;

			if (username == "") 
			{
				alert("Username should not be blank!");
                valid = false;
			}
			if (password == "") 
			{
				alert("Password should not be blank!");
                valid = false;
			}
            return valid;
        }
    </script>
</head> 
<body> 
    <div class="grid-container">
     <div id=header>     
     <img src="IMAGES/Welcome.png">
     <h3>Leave Application Management System</h3>
    </div> 
    <div id="Forms">
   
    <br><b>User Log In</b><br><br>
        <form name="LoginForm" action="UserLoginHandling.php" method="POST" onsubmit="return validateform()"> 
        <label for="username">Username:</label>
        <input type="text" id="username" name="username">
        <br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password">
        <br><br>
        <input type="submit" value="Login">
    </form> 
    </div>
    </div>
</body> 
</html> 