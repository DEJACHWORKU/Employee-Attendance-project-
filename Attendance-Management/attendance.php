<?php
// Database connection parameters
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "attendance"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize messages
$success_message = "";
$error_message = "";

// Handle attendance submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the date and attendance keys exist in $_POST
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    $attendance = isset($_POST['attendance']) ? $_POST['attendance'] : [];

    if (!empty($date) && !empty($attendance)) {
        $all_success = true; // Track overall success

        foreach ($attendance as $employee_id => $status) {
            // Prepare statement to fetch full name
            $name_stmt = $conn->prepare("SELECT full_name FROM attendance_records WHERE employee_id = ?");
            $name_stmt->bind_param("s", $employee_id);
            $name_stmt->execute();
            $name_stmt->bind_result($full_name);
            $name_stmt->fetch();
            $name_stmt->close();

            // Prepare and execute attendance record insertion
            $stmt = $conn->prepare("INSERT INTO employees (employee_id, full_name, date, status) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssss", $employee_id, $full_name, $date, $status);
                if (!$stmt->execute()) {
                    $error_message = "Error recording attendance for $full_name: " . $stmt->error;
                    $all_success = false; // Mark as failed if any record fails
                }
                $stmt->close();
            } else {
                $error_message = "Error preparing statement: " . $conn->error;
                $all_success = false;
            }
        }

        if ($all_success) {
            $success_message = "Attendance recorded successfully for all employees."; // Set success message
        }
    } else {
        $error_message = "Please select a date and attendance status.";
    }
}

// Fetch all employees from the attendance_records table
$employees_result = $conn->query("SELECT DISTINCT employee_id, full_name FROM attendance_records");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance</title>
    <link rel="stylesheet" href="style/attendance.css">
</head>
<body>
    <div class="container">
        <h1>Employee Attendance Record</h1>
        <form action="attendance.php" method="POST">
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Full Name</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($employees_result && $employees_result->num_rows > 0): ?>
                        <?php while ($row = $employees_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['employee_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td>
                                    <select name="attendance[<?php echo htmlspecialchars($row['employee_id']); ?>]">
                                        <option value="Present">Present</option>
                                        <option value="Absent">Absent</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="no-records">No employees found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <input type="submit" value="Submit Attendance">
            <a href="index.php"> Logout</a>
        </form>

        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>