<?php
// Database connection parameters
$servername = "localhost"; // Change if necessary
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "attendance"; // Change if necessary

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all employee records
$result = $conn->query("SELECT employee_record_number, date, employee_id, full_name, position FROM attendance_records");

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Information Report</title>
    <link rel="stylesheet" href="style/report.css">
</head>
<body>
    <h1>Employee Information Report</h1>
    <table>
        <thead>
            <tr>
                <th> Number</th>
                <th>Date</th>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Position</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['employee_record_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['employee_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['position']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-records">No employee records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="no-print">
        <button onclick="window.print();">Print All Info</button>
        <a href="view.php">Back to Employee Records</a>
        <a href="index.php">Logout</a>
    </div>
</body>
</html>