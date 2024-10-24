<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once('includes/database_connect.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $groupId = $_POST['group_id'] ?? 0;
    $taskTitle = $_POST['group_task_title'] ?? '';
    $taskDesc = $_POST['group_task_desc'] ?? '';
    $dateStarted = $_POST['group_date_created'] ?? date('Y-m-d');  // Default to current date if not provided
    $dateDeadline = $_POST['group_date_deadline'] ?? date('Y-m-d');  // Default to current date if not provided
    $priority = $_POST['group_task_priority'] ?? 'Medium';  // Default to 'Medium' if not provided
    $status = $_POST['group_task_status'] ?? 0;  // Default to '0' (Incomplete) if not provided
    $assignedTo = $_POST['assigned_to'] ?? NULL;  // Default to NULL if not provided

    if ($groupId > 0) {
        $stmt = $db->prepare('INSERT INTO Group_Tasks (group_id, group_task_title, group_task_desc, group_date_created, group_date_deadline, group_task_priority, group_task_status, assigned_to) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bindValue(1, $groupId, SQLITE3_INTEGER);
        $stmt->bindValue(2, $taskTitle, SQLITE3_TEXT);
        $stmt->bindValue(3, $taskDesc, SQLITE3_TEXT);
        $stmt->bindValue(4, $dateStarted, SQLITE3_TEXT);
        $stmt->bindValue(5, $dateDeadline, SQLITE3_TEXT);
        $stmt->bindValue(6, $priority, SQLITE3_TEXT);
        $stmt->bindValue(7, $status, SQLITE3_INTEGER);
        $stmt->bindValue(8, $assignedTo, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            header("Location: manage_group.php?group_id=" . $groupId);
            exit;
        } else {
            echo "Error adding task: " . $db->lastErrorMsg();
        }
    } else {
        echo "Error: Group ID is invalid.";
    }
} else {
    header("Location: manage_group.php");
    exit;
}
?>
