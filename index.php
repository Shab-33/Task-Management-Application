<?php 

session_start();
 
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Setting the viewport for responsive design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management Application</title>
    <!-- Linking the external CSS stylesheet -->
    <link rel="stylesheet" href="Styles/style.css">
</head>
<body>

<?php

    // Including the database connection file and other necessary files
    require_once 'includes/database_connect.php';
    require_once 'sidenav.php';


    // Query for individual tasks
    $individualTasksStmt = $db->prepare('SELECT * FROM Individual_tasks WHERE user_id = ?');
    $individualTasksStmt->bindValue(1, $_SESSION['user_id'], SQLITE3_INTEGER);
    $individualResults = $individualTasksStmt->execute();

    // Query for group tasks
    $groupTasksStmt = $db->prepare('SELECT gt.* FROM Group_Tasks gt INNER JOIN "Group" g ON gt.group_id = g.group_id WHERE g.admin_id = ? OR gt.assigned_to = ?');
    $groupTasksStmt->bindValue(1, $_SESSION['user_id'], SQLITE3_INTEGER);
    $groupTasksStmt->bindValue(2, $_SESSION['user_id'], SQLITE3_TEXT);
    $groupResults = $groupTasksStmt->execute();

?>
<div id="main-content">
    <h2>Dashboard</h2>

    <!-- Section for Individual Tasks -->
    <section>
        <h3>Individual Tasks</h3>
        <table>
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($task = $individualResults->fetchArray(SQLITE3_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['task_title']); ?></td>
                        <td><?php echo htmlspecialchars($task['task_desc']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- Section for Group Tasks -->
    <section>
        <h3>Group Tasks</h3>
        <table>
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($task = $groupResults->fetchArray(SQLITE3_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['group_task_title']); ?></td>
                        <td><?php echo htmlspecialchars($task['group_task_desc']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

</body>
</html>
    
?>

</body>
</html>