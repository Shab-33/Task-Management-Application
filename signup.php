<?php
session_start();
require_once('includes/database_connect.php'); 

// Handling the signup submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_fname = $_POST['user_fname'];
    $user_lname = $_POST['user_lname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password

    // Preparing SQL statement to prevent SQL injection
    $stmt = $db->prepare("INSERT INTO USER (user_fname, user_lname, username, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bindValue(1, $user_fname, SQLITE3_TEXT);
    $stmt->bindValue(2, $user_lname, SQLITE3_TEXT);
    $stmt->bindValue(3, $username, SQLITE3_TEXT);
    $stmt->bindValue(4, $email, SQLITE3_TEXT);
    $stmt->bindValue(5, $password, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        echo "<p>Registration successful!</p>";
    } else {
        echo "<p>Registration failed.</p>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Styles/sign-up-style.css">
    <title>Sign Up</title>
</head>
<body>
    <h2>Sign Up</h2>
    <form action="signup.php" method="POST">
        <input type="text" name="user_fname" placeholder="First Name" required>
        <input type="text" name="user_lname" placeholder="Last Name" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Register">
    </form>
    <div class="signup-button">
    <p>Already have an account?</p>
    <button onclick="window.location.href='login.php';">Login</button>
</div>
</body>
</html>
