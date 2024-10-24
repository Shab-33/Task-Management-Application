<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id'])) {
    include('../includes/database_connect.php');

    $task_id = filter_input(INPUT_POST, 'task_id', FILTER_SANITIZE_NUMBER_INT);
    $task_title = $_POST['task_title'];
    $task_desc = $_POST['task_desc'];
    $date_started = $_POST['date_started'];
    $date_deadline = $_POST['date_deadline'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];  

    $stmt = $db->prepare("UPDATE Individual_tasks SET task_title = ?, task_desc = ?, date_started = ?, date_deadline = ?, priority = ?, is_complete = ? WHERE individual_task_id = ?");
    $stmt->bindValue(1, $task_title, SQLITE3_TEXT);
    $stmt->bindValue(2, $task_desc, SQLITE3_TEXT);
    $stmt->bindValue(3, $date_started, SQLITE3_TEXT);
    $stmt->bindValue(4, $date_deadline, SQLITE3_TEXT);
    $stmt->bindValue(5, $priority, SQLITE3_TEXT);
    $stmt->bindValue(6, $status, SQLITE3_INTEGER); 
    $stmt->bindValue(7, $task_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        header("Location: ../home.php?message=Task Updated Successfully");
        exit;
    } else {
        echo "Error: Could not update task. " . $db->lastErrorMsg();
    }
} else {
    header("Location: ../index.php?error=Invalid Request");
    exit;
}
?>
