<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['group_task_id'])) {
    include('../includes/database_connect.php'); 

    // Retrieving and sanitize input
    $group_task_id = filter_input(INPUT_POST, 'group_task_id', FILTER_SANITIZE_NUMBER_INT);
    $group_task_title = filter_input(INPUT_POST, 'group_task_title', FILTER_SANITIZE_STRING);
    $group_task_desc = filter_input(INPUT_POST, 'group_task_desc', FILTER_SANITIZE_STRING);
    $group_date_created = filter_input(INPUT_POST, 'group_date_created', FILTER_SANITIZE_STRING);
    $group_date_deadline = filter_input(INPUT_POST, 'group_date_deadline', FILTER_SANITIZE_STRING);
    $group_task_priority = filter_input(INPUT_POST, 'group_task_priority', FILTER_SANITIZE_STRING);
    $group_task_status = filter_input(INPUT_POST, 'group_task_status', FILTER_SANITIZE_NUMBER_INT);

    // Preparing the update statement including the status field
    $stmt = $db->prepare("UPDATE Group_Tasks SET group_task_title = ?, group_task_desc = ?, group_date_created = ?, group_date_deadline = ?, group_task_priority = ?, group_task_status = ? WHERE group_task_id = ?");
    
    // Binding parameters
    $stmt->bindValue(1, $group_task_title, SQLITE3_TEXT);
    $stmt->bindValue(2, $group_task_desc, SQLITE3_TEXT);
    $stmt->bindValue(3, $group_date_created, SQLITE3_TEXT);
    $stmt->bindValue(4, $group_date_deadline, SQLITE3_TEXT);
    $stmt->bindValue(5, $group_task_priority, SQLITE3_TEXT);
    $stmt->bindValue(6, $group_task_status, SQLITE3_INTEGER); 
    $stmt->bindValue(7, $group_task_id, SQLITE3_INTEGER);

    // Executing the statement and checking for success
    if($stmt->execute()) {
        // Redirecting to manage_group.php with a success message
        header("Location: ../manage_group.php?message=Task Updated Successfully");
        exit();
    } else {
        // Printing error message from SQLite
        echo "Error: Could not update task. " . $db->lastErrorMsg();
    }
} else {
    // Redirecting to manage_group.php if the form wasn't submitted correctly
    header("Location: ../manage_group.php?error=Invalid Request");
    exit();
}
?>