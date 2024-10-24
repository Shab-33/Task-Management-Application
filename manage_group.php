<?php
session_start();  

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Checking if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once('includes/database_connect.php');

// Setting $groupId after including the database connection
$groupId = $_GET['group_id'] ?? 0; // The group_id should be passed as a query parameter

//echo "User ID: " . $_SESSION['user_id'] . "<br>";
//echo "Group ID: " . $groupId . "<br>";

// If group_id is not set or zero, redirect back to a safe page, like the dashboard
if ($groupId == 0) {
    header('Location: index.php');
    exit;
}

// Fetching group details
$stmt = $db->prepare('SELECT group_name FROM "Group" WHERE group_id = ? AND admin_id = ?');
$stmt->bindValue(1, $groupId, SQLITE3_INTEGER);
$stmt->bindValue(2, $_SESSION['user_id'], SQLITE3_INTEGER);
$result = $stmt->execute();
$group = $result->fetchArray(SQLITE3_ASSOC);

// If the group doesn't exist or the logged-in user is not the admin, redirect or show an error
if (!$group) {
    header("Location: error_page.php");
    exit;
}

// Fetching group tasks dynamically based on user's group
if (isset($_SESSION['user_id']) && isset($_SESSION['last_managed_group_id'])) {
    $groupId = $_SESSION['last_managed_group_id'];

    $tasksStmt = $db->prepare('SELECT Group_Tasks.*, USER.username AS assigned_username FROM Group_Tasks LEFT JOIN USER ON Group_Tasks.assigned_to = USER.user_id WHERE group_id = ? ORDER BY group_task_id DESC');
    $tasksStmt->bindValue(1, $groupId, SQLITE3_INTEGER);
    $tasksResult = $tasksStmt->execute();

    $tasks = [];
    while ($task = $tasksResult->fetchArray(SQLITE3_ASSOC)) {
        $tasks[] = $task;
    }
    // Handling the scenario where no tasks are found
} else {
    echo "No group selected or you're not an admin of any group.";
}

// Handling sorting
$sort = $_GET['sort'] ?? 'group_task_id';
$order = $_GET['order'] ?? 'asc';  // Set default order to 'asc' if not specified
$next_order = $order == 'asc' ? 'desc' : 'asc';


// SQL query with dynamic ORDER BY
$tasksStmt = $db->prepare("SELECT Group_Tasks.*, USER.username AS assigned_username FROM Group_Tasks LEFT JOIN USER ON Group_Tasks.assigned_to = USER.user_id WHERE group_id = ? ORDER BY $sort $order");
$tasksStmt->bindValue(1, $groupId, SQLITE3_INTEGER);
$tasksResult = $tasksStmt->execute();

$tasks = [];
while ($task = $tasksResult->fetchArray(SQLITE3_ASSOC)) {
    $tasks[] = $task;
}

// Sorting order and toggle 
function sort_link($current_sort, $current_order, $column, $display_name, $default_order = 'asc') {
    // Determine the next order based on current order
    $next_order = $current_order == 'asc' ? 'desc' : 'asc';

    // Checking if the current sorting column matches this column, adjust the next order accordingly
    if ($current_sort != $column) {
        $next_order = $default_order;
    }

    return "<a href='?group_id={$GLOBALS['groupId']}&sort=$column&order=$next_order'>$display_name</a>";
}


// Adjusting the SQL based on the sort and order parameters
$sort_column = 'group_task_id';  // Default sort column
if (in_array($sort, ['group_date_created', 'group_date_deadline', 'group_task_priority', 'group_task_status'], true)) {
    $sort_column = $sort;
}

// Special handling for sorting priorities and status
if ($sort == 'group_task_priority') {
    // Mapping priorities to numeric values for sorting if stored as strings
    $sort_column = "CASE group_task_priority WHEN 'Low' THEN 1 WHEN 'Medium' THEN 2 WHEN 'High' THEN 3 END";
} elseif ($sort == 'group_task_status') {
    $sort_column = "group_task_status";  // Assuming status is already numeric
}

$tasksStmt = $db->prepare("SELECT Group_Tasks.*, USER.username AS assigned_username FROM Group_Tasks LEFT JOIN USER ON Group_Tasks.assigned_to = USER.user_id WHERE group_id = ? ORDER BY $sort_column $order");
$tasksStmt->bindValue(1, $groupId, SQLITE3_INTEGER);
$tasksResult = $tasksStmt->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Group</title>
    <link rel="stylesheet" href="Styles/manage-group-style.css">
</head>
<body>
    <!-- Side Navigation -->
    <?php include('sidenav.php'); ?>


    <div class="add-task-form">

        <h3>Add New Task</h3>
        <form action="add_task_to_group.php" method="post">
            <input type="text" name="group_task_title" placeholder="Task Title" required><br>
            <textarea name="group_task_desc" placeholder="Task Description"></textarea><br>
            <input type="date" name="group_date_created" placeholder="Start Date"><br>
            <input type="date" name="group_date_deadline" placeholder="Deadline"><br>
            <select name="group_task_priority">
                <option value="High">High</option>
                <option value="Medium" selected>Medium</option>
                <option value="Low">Low</option>
            </select><br>
            <select name="group_task_status">
                <option value="0" selected>Incomplete</option>
                <option value="1">Complete</option>
            </select><br>
            <select name="assigned_to" required>
            <?php
            // Fetching all users
            $users = $db->query('SELECT user_id, username FROM USER');
            while ($user = $users->fetchArray()) {
                echo "<option value='" . htmlspecialchars($user['user_id']) . "'>" . htmlspecialchars($user['username']) . "</option>";
            }
            ?>
            <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($groupId); ?>">
            <button type="submit">Add Task</button>
    </div>
        
            <h3>Group Tasks</h3>
            <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Description</th>
                    <th><?= sort_link($sort, $order, 'group_date_created', 'Date Started', 'asc') ?></th>
                    <th><?= sort_link($sort, $order, 'group_date_deadline', 'Deadline', 'asc') ?></th>
                    <th><?= sort_link($sort, $order, 'group_task_priority', 'Priority', 'desc') ?></th>
                    <th><?= sort_link($sort, $order, 'group_task_status', 'Status', 'desc') ?></th>
                    <th>Assigned To</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                            <td><?php echo htmlspecialchars($task['group_task_id']); ?></td>
                        <td><?php echo htmlspecialchars($task['group_task_title']); ?></td>
                        <td><?php echo htmlspecialchars($task['group_task_desc']); ?></td>
                        <td><?php echo htmlspecialchars($task['group_date_created']); ?></td>
                        <td><?php echo htmlspecialchars($task['group_date_deadline']); ?></td>
                        <td><?php echo htmlspecialchars($task['group_task_priority']); ?></td>
                        <td><?php echo htmlspecialchars($task['group_task_status'] == 1 ? 'Complete' : 'Incomplete'); ?></td>
                        <td><?php echo htmlspecialchars($task['assigned_username'] ?? 'Not Assigned'); ?></td>
                        <td>
                            <form action="scripts/edit_group_task.php" method="post" style="display:inline;">
                                <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['group_task_id']); ?>">
                                <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($groupId); ?>">
                                <button type="submit">Edit</button>
                            </form>
                            <form action="scripts/delete_group_task.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['group_task_id']); ?>">
                                <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($groupId); ?>">
                                <button id="delete-button" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        

    
        <!-- Adding Task Form -->
        
  
            <h3>Users Assigned to Tasks</h3>
            <table>
                <thead>
                    <tr>
                        <th>Member Name</th>
                        <th>Task</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Querying to fetch only users assigned to tasks within this group
                    $assignedMembersQuery = $db->prepare("SELECT u.username, gt.group_task_title, gt.group_task_id FROM USER u JOIN Group_Tasks gt ON u.user_id = gt.assigned_to WHERE gt.group_id = ?");
                    $assignedMembersQuery->bindValue(1, $groupId, SQLITE3_INTEGER);
                    $result = $assignedMembersQuery->execute();

                    while ($member = $result->fetchArray(SQLITE3_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($member['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['group_task_title']) . "</td>";
                        echo "<td>";
                        echo "<form action='scripts/remove_member.php' method='post' style='display:inline;'>";
                        echo "<input type='hidden' name='task_id' value='" . $member['group_task_id'] . "'>";
                        echo "<button type='submit' onclick='return confirm(\"Are you sure you want to unassign this task?\");'>Unassign</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

    </div>
</body>
</html>
