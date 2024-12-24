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

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_stmt = $conn->prepare("DELETE FROM attendance_records WHERE employee_id = ?");
    $delete_stmt->bind_param("s", $delete_id);
    $delete_stmt->execute();
    $delete_stmt->close();
}

// Fetch employee records
$result = $conn->query("SELECT * FROM attendance_records");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employee Info</title>
    <link rel="stylesheet" href="style/view.css">
</head>
<body>
    <div class="container">
        <h1>Employee Records</h1>
        <table>
            <thead>
                <tr>
                    <th> Number</th>
                    <th>Date</th>
                    <th>Employee ID</th>
                    <th>Full Name</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['employee_record_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['employee_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['position']); ?></td>
                            <td class="actions">
                                <a href="edit.php?employee_id=<?php echo urlencode($row['employee_id']); ?>" class="edit-btn">Edit</a>
                                <a href="?delete_id=<?php echo urlencode($row['employee_id']); ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-records">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="button-container">
            <a href="employ.php" class="add-btn">Add New Employee Record</a>
            <a href="report.php" class="report-btn">Print All Employee Info</a>
            <a href="index.php" class="logout-btn">Logout</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>