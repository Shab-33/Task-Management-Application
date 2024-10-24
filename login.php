<?php
session_start();
// Display any error messages based on the query parameter
$error = '';
if (isset($_GET['error']) && $_GET['error'] == 'invalid_credentials') {
    $error = 'Invalid username or password.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Styles/login-style.css">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if ($error): ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="login_handler.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>
    <div class="signup-button">
        <p>Don't have an account?</p>
        <button onclick="window.location.href='signup.php';">Sign up</button>
    </div>
</body>
</html>
