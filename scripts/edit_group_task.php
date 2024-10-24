<?php
session_start();
include('../includes/database_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    $stmt = $db->prepare('SELECT * FROM Group_Tasks WHERE group_task_id = ?');
    $stmt->bindValue(1, $task_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $task = $result->fetchArray(SQLITE3_ASSOC);

    if (!$task) {
        echo "Task not found.";
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
</head>
<body>
    <form action="update_group_task.php" method="post">
        <input type="hidden" name="group_task_id" value="<?php echo htmlspecialchars($task['group_task_id']); ?>">
        <input type="text" name="group_task_title" value="<?php echo htmlspecialchars($task['group_task_title']); ?>" required>
        <input type="date" name="group_date_created" value="<?php echo htmlspecialchars($task['group_date_created']); ?>">
        <input type="date" name="group_date_deadline" value="<?php echo htmlspecialchars($task['group_date_deadline']); ?>">
        <textarea name="group_task_desc"><?php echo htmlspecialchars($task['group_task_desc']); ?></textarea>
        <select name="group_task_priority" required>
            <option value="High" <?php echo ($task['group_task_priority'] === 'High') ? 'selected' : ''; ?>>High</option>
            <option value="Medium" <?php echo ($task['group_task_priority'] === 'Medium') ? 'selected' : ''; ?>>Medium</option>
            <option value="Low" <?php echo ($task['group_task_priority'] === 'Low') ? 'selected' : ''; ?>>Low</option>
        </select>
        <select name="group_task_status" required>
            <option value="0" <?php echo ($task['group_task_status'] == 0) ? 'selected' : ''; ?>>Incomplete</option>
            <option value="1" <?php echo ($task['group_task_status'] == 1) ? 'selected' : ''; ?>>Complete</option>
        </select>
        <button type="submit">Update Task</button>
    </form>
</body>
</html>
