<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
$servername = "localhost"; // Your server name
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "employ_attendance"; // Your database name

// Create connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_msg = ""; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username and password are set
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        $error_msg = "Username and password cannot be empty.";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT password FROM admininfo WHERE username = ?");
        $stmt->bind_param("s", $username); // 's' specifies the variable type => 'string'
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($db_password);
            $stmt->fetch();

            if ($password === $db_password) {
                // Successful login
                session_start(); // Start a session
                $_SESSION['username'] = $username; // Store username in session
                header("Location: main.php"); // Redirect to desired location
                exit();
            } else {
                $error_msg = "Invalid username or password.";
            }
        } else {
            $error_msg = "Invalid username or password.";
        }

        $stmt->close();
    }
}

$conn->close(); // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employ Attendance System</title>
    <link rel="stylesheet" href="style/css.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <script>
        // Function to hide error message after 5 seconds
        function hideErrorMessage() {
            const errorElement = document.getElementById('error-message');
            if (errorElement) {
                setTimeout(() => {
                    errorElement.style.display = 'none';
                }, 5000); // 5000 milliseconds = 5 seconds
            }
        }

        // Function to toggle password visibility
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('password-toggle');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.textContent = 'Hide Password';
            } else {
                passwordInput.type = 'password';
                passwordToggle.textContent = 'Show Password';
            }
        }
    </script>
</head>

<body>
    <header>
        <img src="img/logo.jpg" alt="Logo" class="logo">
        <h1>YA-WATER AND JUICE FACTROY</h1>
    </header>

    <main>
        <div class="form-container">
        <h2>EMPLOY ATTENDANCE SYSTEM</h2>
        <h3>Admin Page</h3>
            <?php if (!empty($error_msg)) : ?>
                <div id="error-message" class="error"><?php echo $error_msg; ?></div>
                <script>
                    hideErrorMessage(); // Call the function to hide the error message
                </script>
            <?php endif; ?>
            <form method="post" class="form-horizontal">
                <div class="form-group">
                    <label for="username" class="control-label">Username</label>
                    <input type="text" name="username" class="form-control" id="username" placeholder="Your username" required />
                </div>

                <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Your password" required />

                </div>
                <button type="submit" class="btn login-button">Login</button>
            </form>
        </div>
    </main>
</body>
</html>