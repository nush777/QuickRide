<?php

include 'connection.php'; // Ensure this file correctly establishes $conn
date_default_timezone_set('Asia/Dhaka');

// --- 1. Define the desired date range ---
$presentDate = new DateTime(); // Today's date
$daysBefore = 5;
$daysAfter = 5;

// Calculate the absolute min and max dates for the desired window
$minDesiredDate = newDate(clone $presentDate, -$daysBefore); // 5 days before today
$maxDesiredDate = newDate(clone $presentDate, +$daysAfter);  // 5 days after today

// Generate a list of all table names that *should* exist
$desiredTables = [];
for ($i = -$daysBefore; $i <= $daysAfter; $i++) {
    $date = newDate(clone $presentDate, $i);
    $desiredTables[dateToString($date)] = true; // Use date as key for quick lookup
}

//echo "Current date: " . $presentDate->format('Y-m-d') . "<br>";
//echo "Desired window: " . $minDesiredDate->format('Y-m-d') . " to " . $maxDesiredDate->format('Y-m-d') . "<br>";
//echo "Number of desired tables: " . count($desiredTables) . "<br>";

// --- 2. Clean up old tables (drop tables that are outside the desired range) ---
//echo "<h2>Cleaning up old tables...</h2>";

// Query to get all existing tables that follow the YYYY_MM_DD pattern
// IMPORTANT: This regex relies on your table naming convention.
$existingTablesQuery = "SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name REGEXP '^[0-9]{4}_[0-9]{2}_[0-9]{2}$'";
$existingTablesResult = $conn->query($existingTablesQuery);

if ($existingTablesResult) {
    while ($row = $existingTablesResult->fetch_assoc()) {
        $tableName = $row['table_name'];
        $tableDate = DateTime::createFromFormat('Y_m_d', $tableName);

        // Check if the table name is actually a valid date and falls outside the desired window
        if ($tableDate && ($tableDate < $minDesiredDate || $tableDate > $maxDesiredDate)) {
            dropTable($tableName); // Drop the table
            //echo "Dropped old table: $tableName<br>";
        }
    }
} else {
    echo "Error fetching existing tables: " . $conn->error . "<br>";
}

// --- 3. Create missing tables (create tables that are needed but don't exist) ---
//echo "<h2>Creating missing tables...</h2>";

foreach ($desiredTables as $tableName => $dummy) {
    if (!checkTable($tableName)) {
        createTable($tableName); // This function also calls insertData
        //echo "Created new table: $tableName<br>";
    } else {
        //echo "Table '$tableName' already exists.<br>"; // Uncomment if you want to see existing ones
    }
}

//echo "<h2>Synchronization complete.</h2>";

// --- Functions (Keep these as they are, but remember the security notes from previous answer) ---

// Function to calculate a new date after a specified number of days
function newDate($date, $days) {
    if ($date instanceof DateTime) {
        $date = clone $date; // Ensure the original date is not modified
        $date->modify($days . ' days'); // Modify based on days
        return $date; // Return DateTime object
    } else {
        throw new Exception('Input must be a DateTime object');
    }
}

// Function to format a date as Y_m_d
function dateToString($date) {
    if ($date instanceof DateTime) {
        return $date->format('Y_m_d'); // Ensure the DateTime object is formatted correctly
    } else {
        throw new Exception('Input must be a DateTime object');
    }
}

// SQL query for creating a seat booking table for a specific date
function insertQuery($tableName) {
    $query = "CREATE TABLE `$tableName` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        bus_code CHAR(4) NOT NULL,
        seat_name CHAR(2) NOT NULL,
        status VARCHAR(255),
        booked_date DATE,
        booked_user_id INT,
        name VARCHAR(255),
        email VARCHAR(255),
        phone_number VARCHAR(15),
        FOREIGN KEY (bus_code) REFERENCES bus(bus_code),
        FOREIGN KEY (booked_user_id) REFERENCES user(id)
    )";
    return $query;
}

// Check if a table exists
function checkTable($tableName) {
    global $conn;
    $query = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        // Handle error: echo "Error preparing statement for checkTable: " . $conn->error;
        return false; // Or throw an exception
    }
    $stmt->bind_param("s", $tableName);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

// Helper to generate seat names
function getSeatNumber($i, $j) {
    return "$i" . chr(65 + $j); // Generate seat names like 1A, 1B, etc.
}

// Insert initial data into a newly created table
function insertData($tableName) {
    global $conn;

    // Fetch all bus codes and capacities
    $query = "SELECT bus_code, capacity FROM bus";
    $buses = $conn->query($query);

    if (!$buses) {
        echo "Error fetching buses: " . $conn->error . "<br>";
        return;
    }

    // Use prepared statement for inserting data to prevent SQL injection
    $insertQuery = "INSERT INTO `$tableName` (bus_code, seat_name, status, booked_date, booked_user_id) VALUES (?, ?, 'free', NULL, NULL)";
    $stmt = $conn->prepare($insertQuery);

    if ($stmt === false) {
        echo "Error preparing statement for $tableName: " . $conn->error . "<br>";
        return;
    }

    foreach ($buses as $bus) {
        for ($i = 1; $i <= 8; $i++) { // Rows 1 to 8
            for ($j = 0; $j <= 3; $j++) { // Columns A to D
                // Skip the last column (D) if bus capacity is less than 32
                if ($j == 3 && $bus['capacity'] != 32) {
                    continue;
                }
                $seatName = getSeatNumber($i, $j);

                $stmt->bind_param("ss", $bus['bus_code'], $seatName); // 'ss' for two string parameters
                if (!$stmt->execute()) {
                    // echo "Error inserting data into '$tableName' for bus '{$bus['bus_code']}', seat '$seatName': " . $stmt->error . "<br>";
                }
            }
        }
    }
    $stmt->close();
    //echo "Data inserted successfully into table: $tableName.<br>";
}

// Drop a table
function dropTable($tableName) {
    global $conn;
    // Table name is derived from a DateTime object, so it's generally safe here.
    // If $tableName could come from user input, this would need prepared statements.
    $dropQuery = "DROP TABLE IF EXISTS `$tableName`";
    $result = $conn->query($dropQuery);
    if (!$result) {
        echo "Error dropping table '$tableName': " . $conn->error . "<br>";
    }
}

// Create a table and populate it with initial data
function createTable($tableName) {
    global $conn;
    // Table name is derived from a DateTime object, so it's generally safe here.
    $createQuery = insertQuery($tableName);
    $result = $conn->query($createQuery);
    if ($result) {
        insertData($tableName);
    } else {
        echo "Error creating table '$tableName': " . $conn->error . "<br>";
    }
}

?>