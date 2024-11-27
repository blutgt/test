<?php
session_start(); // Start session to store user information

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "logindetails"); // Specify database

if (isset($_POST['login_btn'])) { // Check if login button is clicked
    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQL query to check if the username exists
    $sql = "SELECT * FROM logindetails WHERE username = '$username'"; // Fix the table name
    $result = mysqli_query($conn, $sql);

    // If there's a result, check the password
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $resultPassword = $row['password'];
        
        // Check if passwords match
        if ($password == $resultPassword) {
            // Store user details in session after successful login
            $_SESSION['user_id'] = $row['id'];  // Assuming 'id' is the primary key in your database
            $_SESSION['username'] = $username;  // Store the username
            header('Location: homepage.php'); // Redirect to homepage after successful login
            exit();
        } else {
            echo "<script>alert('Login unsuccessful');</script>"; // If password doesn't match
        }
    } else {
        echo "<script>alert('Username not found');</script>"; // If username doesn't exist
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Global styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #2c2c2c; /* Default dark grey background */
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            transition: background 0.5s ease; /* Smooth transition for background change */
            background-image: url('img/bback1.png'); /* Set the background image from the img folder */
            background-size: cover;  /* Ensures the image covers the full screen */
            background-repeat: no-repeat; /* Prevent image repeat */
            background-position: center center; /* Keep the image centered */
        }

        h1 {
            font-size: 3rem;
            text-align: center;
            color: #0070ba; /* Normal blue */
            text-shadow: none; /* Removed glowing effect */
        }

        form {
            background-color: #121212; /* Dark grey background for the form */
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 0 20px #00aaff; /* Always glowing effect */
            border: 2px solid #00aaff; /* Glowing blue border */
            position: relative;
        }

        /* Input and button styles */
        .textBoxdiv {
            margin: 15px 0;
            position: relative;
        }

        .textBoxdiv input {
            width: 100%; /* Ensures inputs take up the full width */
            padding: 12px;
            font-size: 16px;
            border: 2px solid #0070ba; /* Glowing blue border */
            border-radius: 5px;
            background-color: #121212;
            color: white;
            outline: none;
            box-sizing: border-box; /* Ensures proper width alignment */
            transition: all 0.3s ease-in-out;
        }

        .textBoxdiv input:focus {
            border-color: #00aaff;
            box-shadow: 0 0 8px #00aaff; /* Glowing effect on focus */
        }

        .loginBtn {
            width: 100%; /* Ensure the button is the same width as the inputs */
            padding: 12px;
            background-color: #2c2c2c; /* Grey background */
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .loginBtn:hover {
            background-color: #555; /* Darker grey on hover */
            box-shadow: 0 0 8px #00aaff; /* Glowing effect on hover */
        }

        .signup {
            margin-top: 20px;
            color: #aaa;
        }

        .signup a {
            color: #00aaff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .signup a:hover {
            color: #0070ba; /* Glowing blue on hover */
        }
    </style>
</head>
<body>
    <form method="POST" action="login.php">
        <h1>Login</h1>
        <div class="textBoxdiv">
            <input type="text" placeholder="Username" name="username" required>
        </div>
        <div class="textBoxdiv">
            <input type="password" placeholder="Password" name="password" required>
        </div>
        <input type="submit" value="Login" class="loginBtn" name="login_btn">
        <div class="signup">
            <p>Don't have an account?</p>
            <a href="register.php">Sign up</a>
        </div>
    </form>
</body>
</html>