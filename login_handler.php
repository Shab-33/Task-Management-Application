<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('includes/database_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT user_id, password FROM USER WHERE username = ?");
    $stmt->bindValue(1, $username, SQLITE3_TEXT);
    $result = $stmt->execute();

    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login success
        $_SESSION['user_id'] = $user['user_id'];
    
        // Logging the user ID
        error_log("User ID: " . $_SESSION['user_id']);
    
        // Querying for a group the user is an admin of and set session variable
        $stmt = $db->prepare('SELECT group_id FROM "Group" WHERE admin_id = ? LIMIT 1');
        $stmt->bindValue(1, $_SESSION['user_id'], SQLITE3_INTEGER);
        $groupResult = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        
        if ($groupResult) {
            $_SESSION['last_managed_group_id'] = $groupResult['group_id'];
            // Log the found group ID
            error_log("Found Group ID: " . $groupResult['group_id']);
        } else {
            // Log if no group is found
            error_log("No group found for user ID: " . $_SESSION['user_id']);
        }
    
        // Redirecting to the index page after setting session variables
        header("Location: index.php");
        exit();
    } else {
        // Login failed
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
} else {
    // If the method is not POST, redirecting back to the login form
    header("Location: login.php");
    exit();
}
    


// Login success
$_SESSION['user_id'] = $user['user_id'];

// Querying for a group the user is an admin of
$stmt = $db->prepare('SELECT group_id FROM "Group" WHERE admin_id = ? LIMIT 1');
$stmt->bindValue(1, $_SESSION['user_id'], SQLITE3_INTEGER);
$groupResult = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
if ($groupResult) {
    $_SESSION['last_managed_group_id'] = $groupResult['group_id'];
}

header("Location: index.php");
exit();


// If the method is not POST, redirect back to the login form
header("Location: login.php");
exit();
?>