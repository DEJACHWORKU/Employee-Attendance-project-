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

// Initialize variables
$report_data = [];
$month = isset($_POST['month']) ? $_POST['month'] : date('m'); // Default to current month
$year = isset($_POST['year']) ? $_POST['year'] : date('Y'); // Default to current year

// Fetch all attendance records for the selected month and year
$stmt = $conn->prepare("SELECT employee_id, full_name, date, status FROM employees WHERE status IS NOT NULL AND MONTH(date) = ? AND YEAR(date) = ?");
$stmt->bind_param("si", $month, $year); // 's' for string, 'i' for integer

if ($stmt->execute()) {
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $report_data[] = $row;
    }
} else {
    echo "SQL Error: " . $stmt->error; // Display SQL error
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <link rel="stylesheet" href="style/print_report.css"> <!-- Add your CSS file -->
    <style>
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px; /* Optional: space above buttons */
        }
        .logout-button {
            margin-left: 10px; /* Adds space between buttons */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Attendance Report</h1>

        <form method="POST">
            <label for="month">Month:</label>
            <select name="month" id="month">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>" <?php echo ($m == $month) ? 'selected' : ''; ?>>
                        <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                    </option>
                <?php endfor; ?>
            </select>

            <label for="year">Year:</label>
            <select name="year" id="year">
                <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo ($y == $year) ? 'selected' : ''; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endfor; ?>
            </select>

            <input type="submit" value="Search">
        </form>

        <?php if ($report_data): ?>
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Full Name</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report_data as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['employee_id']); ?></td>
                            <td><?php echo htmlspecialchars($record['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['date']); ?></td>
                            <td><?php echo htmlspecialchars($record['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="button-container">
                <button onclick="window.print()" class="no-print">Print Report</button>
                <button onclick="window.location.href='index.php'" class="logout-button">Logout</button>
            </div>
        <?php else: ?>
            <p>No attendance records found for this month and year.</p>
        <?php endif; ?>
    </div>
</body>
</html>