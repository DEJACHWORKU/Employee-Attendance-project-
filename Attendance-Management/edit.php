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
$employee_id = "";
$date = "";
$full_name = "";
$position = "";
$error_message = "";
$success_message = "";

// Get employee details for editing
if (isset($_GET['employee_id'])) {
    $employee_id = $_GET['employee_id'];
    $stmt = $conn->prepare("SELECT * FROM attendance_records WHERE employee_id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $employee_record_number = $row['employee_record_number'];
        $date = $row['date'];
        $full_name = $row['full_name'];
        $position = $row['position'];
    } else {
        $error_message = "Employee not found.";
    }
    $stmt->close();
}

// Handle form submission for updating the record
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $employee_record_number = trim($_POST['employee_record_number']);
    $date = $_POST['date'];
    $full_name = trim($_POST['full_name']);
    $position = trim($_POST['position']);

    // Update the record
    $stmt = $conn->prepare("UPDATE attendance_records SET employee_record_number = ?, date = ?, full_name = ?, position = ? WHERE employee_id = ?");
    $stmt->bind_param("sssss", $employee_record_number, $date, $full_name, $position, $employee_id);

    if ($stmt->execute()) {
        $success_message = "Record updated successfully.";
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee Info</title>
    <link rel="stylesheet" href="style/edit.css">
</head>
<body>
    <div class="container">
        <h1>Edit Employee Info</h1>
        <form action="edit.php" method="POST">
            <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee_id); ?>">
            <div class="form-group">
                <label for="employee_record_number">Number:</label>
                <input type="text" id="employee_record_number" name="employee_record_number" value="<?php echo htmlspecialchars($employee_record_number); ?>" required>
            </div>
            <div class="form-group">
                <label for="date">Date of Record:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>
            </div>
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="position">Position:</label>
                <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($position); ?>" required>
            </div>
            <div class="button-container">
                <input type="submit" value="Update" class="submit-btn">
                <a href="view.php" class="cancel-btn">Cancel</a>
            </div>

            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>