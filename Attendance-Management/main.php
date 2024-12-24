<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/main.css">
    <title>Employee Management</title>
</head>
<body>
    <div class="container">
        <h1>CHOOSE YOUR MANAGEMENT</h1>
        <form action="employ.php" method="get">
            <button type="submit" class="button">Record Employee</button>
        </form>
        <form action="attendance.php" method="post">
            <button type="submit" class="button">Check Attendance</button>
        </form>
        <form action="print_report.php" method="post">
            <button type="submit" class="button">Print Report</button>
        </form>
    </div>
</body>
</html>