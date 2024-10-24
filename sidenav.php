<?php
// Checking if the session has not been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Using require_once to include the database connection file
require_once 'includes/database_connect.php';

// Calling function without redeclaring it
$userIsAdminOfGroup = isset($_SESSION['user_id']) && isUserAdminOfAnyGroup($db, $_SESSION['user_id']);

//echo "Debug - User ID: " . $_SESSION['user_id'] . "<br>"; // Show the user ID
//echo "Debug - Is Admin of Any Group: " . (isUserAdminOfAnyGroup($db, $_SESSION['user_id']) ? 'Yes' : 'No') . "<br>";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management Application sidenav</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://kit.fontawesome.com/6360516e43.js" crossorigin="anonymous"></script>
</head>
<body>

    <!-- Creating the sidebar navigation (sidenav) -->
    <div class="wrapper">
        <div class="sidenav">
            <!-- Displaying the site title or logo -->
            <h1>Task Management</h1>
            <!-- Navigation list -->
            <ul>
                <!-- Navigation item for Home -->
                <li><a  href="/Project2_Shehab_32016884/index.php"><i class="fa-solid fa-house"></i> Dashboard </a></li>
                <!-- Navigation item for Personal Page -->
                <li><a  href="/Project2_Shehab_32016884/home.php"><i class="fa-solid fa-user"></i> Personal </a></li>

                <li>
                <?php if (isset($_SESSION['last_managed_group_id'])): ?>
                    <a href="/Project2_Shehab_32016884/manage_group.php?group_id=<?php echo $_SESSION['last_managed_group_id']; ?>"><i class="fa-solid fa-user-group"></i> Group </a>
                <?php else: ?>
                    <a href="/Project2_Shehab_32016884/group.php"><i class="fa-solid fa-user-group"></i> Group </a>
                <?php endif; ?>
                </li>
                <!-- Navigation item for Group Page -->
                
            </ul>
            <!-- Social media links -->
            <div class="social-media">
                <a href="logout.php">Logout</a>

                <a href="#"><i class="fa-brands fa-facebook"></i></a>
                <a href="#"><i class="fa-brands fa-discord"></i></a>
                <a href="#"><i class="fa-brands fa-twitter"></i></a>
            </div>
        </div>
    </div>
</body>
</html>