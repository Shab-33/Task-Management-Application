<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id'])) {
    include('../includes/database_connect.php'); 

    $task_id = $_POST['task_id'];

    // Preparing the delete statement
    $stmt = $db->prepare('DELETE FROM Individual_tasks WHERE individual_task_id = ?');
    $stmt->bindValue(1, $task_id, SQLITE3_INTEGER);

    // Executing and checking if successful
    if ($stmt->execute()) {
        // Redirecting back to home.php after deletion
        header("Location: ../home.php");
        exit;
    } else {
        echo "Error: Could not delete task. " . $db->lastErrorMsg();
    }
} else {
    // Redirecting to home if accessed directly or without task_id
    header("Location: ../index.php");
    exit;
}
?>n
