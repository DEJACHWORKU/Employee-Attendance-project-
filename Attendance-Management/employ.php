<?php
session_start(); // Start session

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance"; 

// Create connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables
    $errors = [];
    $employee_record_number = trim($_POST['employee_record_number'] ?? '');
    $date = $_POST['date'] ?? '';
    $employee_id = trim($_POST['employee_id'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $position = trim($_POST['position'] ?? '');

    // Validation logic remains the same...
    // (Include your validation code here)

    if (empty($errors)) {
        // Check if the employee record already exists
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM attendance_records WHERE employee_id = ?");
        $check_stmt->bind_param("s", $employee_id);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count > 0) {
            $_SESSION['error_message'] = "Employee ID already exists.";
        } else {
            // Insert record
            $stmt = $conn->prepare("INSERT INTO attendance_records (employee_record_number, date, employee_id, full_name, position) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $employee_record_number, $date, $employee_id, $full_name, $position);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Record added successfully.";
            } else {
                $_SESSION['error_message'] = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        // Close connection
        $conn->close();

        // Redirect to the same page to avoid resubmission
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error_message'] = implode(", ", $errors);
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Record</title>
    <link rel="stylesheet" href="style/employ.css">
    <script>
    function adjustStyles() {
        const container = document.querySelector('.container');
        const screenWidth = window.innerWidth;

        if (screenWidth <= 600) {
            container.style.padding = '10px';
            container.style.fontSize = '0.9em';
        } else {
            container.style.padding = '20px';
            container.style.fontSize = '1em';
        }
    }
    adjustStyles();
    window.addEventListener('resize', adjustStyles);
    </script>
</head>
<body>
    <div class="container">
        <h1>Record Your Employee</h1>
        <form action="" method="POST" id="employeeForm">
            <div class="form-group">
                <label for="employee_record_number">Number:</label>
                <input type="text" id="employee_record_number" name="employee_record_number" required>
            </div>
            <div class="form-group">
                <label for="date">Date of Record:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="employee_id">Employee ID:</label>
                <input type="text" id="employee_id" name="employee_id" required>
            </div>
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="position">Position:</label>
                <input type="text" id="position" name="position" required>
            </div>
            <div class="button-container">
                <input type="submit" value="Save" class="submit-btn">
                <a href="view.php" class="view-btn">View Employee Info</a>
                <!-- Logout Button -->
                <button type="button" onclick="window.location.href='index.php'" class="logout-btn">Logout</button>
            </div>

            <?php if (!empty($success_message)): ?>
                <p class="success-message" id="successMessage"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <p class="error-message" id="errorMessage"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
    </div>

    <script>
        <?php if (!empty($success_message)): ?>
            setTimeout(function() {
                document.getElementById('successMessage').style.opacity = '0';
            }, 3000); 
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            setTimeout(function() {
                document.getElementById('errorMessage').style.opacity = '0';
            }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>