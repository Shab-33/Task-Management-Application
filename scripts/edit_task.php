<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id'])) {
    include('../includes/database_connect.php'); 

    $task_id = $_POST['task_id'];

    // Fetching the task details
    $stmt = $db->prepare('SELECT * FROM Individual_tasks WHERE individual_task_id = ?');
    $stmt->bindValue(1, $task_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $task = $result->fetchArray();

    // Checking if task exists
    if (!$task) {
        echo "Task not found.";
        exit;
    }
} else {
    // Redirecting to home if accessed directly or task_id is not set
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/edit-home-style.css">
    <title>Edit Task</title>
</head>
<body>
    <?php include('../sidenav.php'); ?>

    <div class="form-area">
        <h2>Edit Task</h2>
        <form action="update_task.php" method="post">
            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['individual_task_id']); ?>">
            
            <label for="task_title">Task Title</label>
            <input type="text" id="task_title" name="task_title" value="<?php echo htmlspecialchars($task['task_title']); ?>" required>
            
            <label for="date_started">Date Started</label>
            <input type="date" id="date_started" name="date_started" value="<?php echo htmlspecialchars($task['date_started']); ?>">
            
            <label for="date_deadline">Date Deadline</label>
            <input type="date" id="date_deadline" name="date_deadline" value="<?php echo htmlspecialchars($task['date_deadline']); ?>">
            
            <label for="task_desc">Description</label>
            <textarea id="task_desc" name="task_desc"><?php echo htmlspecialchars($task['task_desc']); ?></textarea>
            
            <label for="priority">Priority</label>
            <select id="priority" name="priority" required>
                <option value="High" <?= $task['priority'] === 'High' ? 'selected' : ''; ?>>High</option>
                <option value="Medium" <?= $task['priority'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="Low" <?= $task['priority'] === 'Low' ? 'selected' : ''; ?>>Low</option>
            </select>
            
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="0" <?= $task['is_complete'] == 0 ? 'selected' : ''; ?>>Incomplete</option>
                <option value="1" <?= $task['is_complete'] == 1 ? 'selected' : ''; ?>>Complete</option>
            </select>
            
            <button type="submit">Update Task</button>
            <button type="button" class="cancel-button" onclick="window.location='../home.php'">Cancel</button>
        </form>
    </div>
</body>
</html>