<?php
// Starting the session
session_start();

require_once('includes/database_connect.php');

// Checking if the user is logged in and has the right to create a group task
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: login.php');
    exit;
}



// Handling form submission for creating a new group task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_group_task'])) {
    $group_name = $_POST['group_name'];
    $admin_id = $_SESSION['user_id']; // Using the logged-in user's ID as the admin ID

    // Preparing the SQL statement for insertion
    $stmt = $db->prepare('INSERT INTO "Group" (group_name, admin_id) VALUES (?, ?)');
    
    // Binding the values to the prepared statement
    $stmt->bindValue(1, $group_name);
    $stmt->bindValue(2, $admin_id);
    
    // Executing the insertion statement
    if ($stmt->execute()) {
        // Insertion was successful, now retrieve the last inserted group_id
        $group_id = $db->lastInsertRowID();
    
        // Storing the last managed group_id in the session
        $_SESSION['last_managed_group_id'] = $group_id;
    
        // Redirecting to manage_group.php with the new group_id as a URL parameter
        header("Location: manage_group.php?group_id=" . $group_id);
        exit(); // Ensure no further code is executed after the redirect
    } 
    else {
        // Handling the error scenario if insertion failed
        echo "Error creating group: " . $db->lastErrorMsg();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/group-style.css">
    <title>Group Tasks</title>
</head>
<body>
    <!-- Side Navigation -->
    <?php include('sidenav.php'); ?>

    <!-- Main Content -->
    <div id="main-content">
        <div id="group-task-widget">
            <!-- Button to show the group task creation form -->
            <button id="create-group-task-btn">Create New Group Task</button>

            <!-- Form to create a new group task -->
            <form id="create-group-task-form" action="group.php" method="post" style="display:none;">
                <input type="text" name="group_name" placeholder="Group Name" required>
                <input type="submit" name="create_group_task" value="Create Group">
            </form>
        </div>

        <!-- JavaScript to show the form when the button is clicked -->
        <script>
            document.getElementById('create-group-task-btn').addEventListener('click', function() {
                document.getElementById('create-group-task-form').style.display = 'block';
            });
        </script>
    </div>
</body>
</html>
