<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once('includes/database_connect.php');

$sort = $_GET['sort'] ?? 'date_started';
$order = $_GET['order'] ?? 'asc';

function sort_link($label, $column, $currentSort, $currentOrder) {
    $dir = $currentSort == $column && $currentOrder == 'asc' ? 'desc' : 'asc';
    $icon = $currentSort == $column ? ($currentOrder == 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : '';
    return "<a href='?sort=$column&order=$dir' class='sort-link'>$label <i class='$icon'></i></a>";
}

$sql = "SELECT individual_task_id, task_title, task_desc, date_started, date_deadline, priority, is_complete FROM Individual_tasks WHERE user_id = ? ORDER BY $sort $order";
$stmt = $db->prepare($sql);
$stmt->bindValue(1, $_SESSION['user_id'], SQLITE3_INTEGER);
$results = $stmt->execute();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Task Management</title>
    <link rel="stylesheet" href="Styles/home-style.css">
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>
    <?php include('sidenav.php'); ?>

    <div class="personal-content">
        <h2>Personal Tasks</h2>
        <div class="personal-tasks">
            <h3>Individual Tasks</h3>
            <button id="home-create-button"onclick="location.href='scripts/create_task.php'">Create New Task</button>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?= sort_link('Task', 'task_title', $sort, $order) ?></th>
                        <th><?= sort_link('Description', 'task_desc', $sort, $order) ?></th>
                        <th><?= sort_link('Date Started', 'date_started', $sort, $order) ?></th>
                        <th><?= sort_link('Date Deadline', 'date_deadline', $sort, $order) ?></th>
                        <th><?= sort_link('Priority', 'priority', $sort, $order) ?></th>
                        <th><?= sort_link('Status', 'is_complete', $sort, $order) ?></th>
                        <th>Action</th>
                    </tr>
                </thead>
<tbody>
                    <?php while ($row = $results->fetchArray()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['individual_task_id']); ?></td>
                            <td><?= htmlspecialchars($row['task_title']); ?></td>
                            <td><?= htmlspecialchars($row['task_desc']); ?></td>
                            <td><?= htmlspecialchars($row['date_started']); ?></td>
                            <td><?= htmlspecialchars($row['date_deadline']); ?></td>
                            <td><?= htmlspecialchars($row['priority']); ?></td>
                            <td><?= $row['is_complete'] == 1 ? 'Complete' : 'Incomplete'; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <form action='scripts/edit_task.php' method='POST' style='display:inline;'>
                                        <input type='hidden' name='task_id' value='<?= htmlspecialchars($row['individual_task_id']); ?>'>
                                        <button type='submit' class="edit-button">Edit</button>
                                    </form>
                                    <form action='scripts/delete_task.php' method='POST' style='display:inline;' onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        <input type='hidden' name='task_id' value='<?= htmlspecialchars($row['individual_task_id']); ?>'>
                                        <button type='submit' class="delete-button">Delete</button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>


</html>

