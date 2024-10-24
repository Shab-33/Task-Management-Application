<?php

// Defining the path to the SQLite database file
$dbPath = 'C:\xampp\htdocs\Project2_Shehab_32016884/data/task_management_application_database.db';

// Attempting to establish a connection to the SQLite database
$db = new SQLite3($dbPath);

// Checking if the connection to the database was successful
if ($db) {
    // echo "Database is successfully connected";
} else {
    // If the connection fails, the script exits and outputs an error message.
    exit("Failed to connect to the database");
}


function isUserAdminOfAnyGroup($db, $userId) {
    $stmt = $db->prepare('SELECT 1 FROM "Group" WHERE admin_id = ? LIMIT 1');
    $stmt->bindValue(1, $userId, SQLITE3_INTEGER);
    $result = $stmt->execute();
    return $result->fetchArray() !== false; // Returns true if any group is found
}

?>

