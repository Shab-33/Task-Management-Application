<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../includes/database_connect.php');

// Checking if we're dealing with a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Displaying submitted POST data (for debugging purposes)
    echo "<pre>POST Data:\n";
    print_r($_POST);
    echo "</pre>";

    // Assigning POST data to variables
    $task_title = $_POST['task_title'] ?? 'Default Title'; // Using a default value if not set
    $task_desc = $_POST['task_desc'] ?? 'Default Description';
    $date_started = $_POST['date_started'] ?? date('Y-m-d'); // Using current date as default
    $date_deadline = $_POST['date_deadline'] ?? date('Y-m-d');
    $priority = $_POST['priority'] ?? 'Medium';
    $status = $_POST['status'] ?? '0'; // Assuming '0' is for incomplete

    if (!isset($_SESSION['user_id'])) {
        die("User ID is not set in the session.");
    }

    // Preparing INSERT statement
    $stmt = $db->prepare('INSERT INTO Individual_tasks (task_title, task_desc, date_started, date_deadline, priority, is_complete, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
    
    // Binding values to statement
    $stmt->bindValue(1, $task_title, SQLITE3_TEXT);
    $stmt->bindValue(2, $task_desc, SQLITE3_TEXT);
    $stmt->bindValue(3, $date_started, SQLITE3_TEXT);
    $stmt->bindValue(4, $date_deadline, SQLITE3_TEXT);
    $stmt->bindValue(5, $priority, SQLITE3_TEXT);
    $stmt->bindValue(6, $status, SQLITE3_TEXT);
    $stmt->bindValue(7, $_SESSION['user_id'], SQLITE3_INTEGER);


    // Executing statement and checking for errors
    if ($stmt->execute()) {
    // The task was successfully added to the database
    // Now redirecting to home.php at the root level of the project
    header("Location: ../home.php");
    exit();
    } else {
    // If the task could not be added, outputing an error message
    echo "Error: Could not add task. " . $db->lastErrorMsg();
    // Closing the database connection
    $db->close();
    $db = null;
    }
    
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task</title>
    <link rel="stylesheet" href="../Styles/create-task-style.css">
</head>
<body>
    <?php include('../sidenav.php'); ?>

    <!-- Trigger Button -->
    <button id="createTaskButton">Create New Task</button>

    <!-- Task Creation Form -->
    <div id="taskFormContainer" class="hidden">
        <form  method="post" id="createTaskForm">
            <h2>Create a New Task</h2>
            <input type="text" name="task_title" placeholder="Title" required><br>
            <textarea name="task_desc" placeholder="Description"></textarea><br>
            <input type="date" name="date_started"><br>
            <input type="date" name="date_deadline"><br>
            <select name="priority">
                <option value="High">High</option>
                <option value="Medium" selected>Medium</option>
                <option value="Low">Low</option>
            </select><br>
            <select name="status">
                <option value="0" selected>Incomplete</option>
                <option value="1">Complete</option>
            </select><br>
            <button type="submit">Add Task</button>
            <button type="button" onclick="toggleForm()">Cancel</button>
        </form>
    </div>

    <script>
        function toggleForm() {
            var formContainer = document.getElementById('taskFormContainer');
            formContainer.classList.toggle('hidden');
        }

        document.getElementById('createTaskButton').addEventListener('click', toggleForm);
    </script>
</body>
</html>
