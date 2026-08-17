<?php

session_start();

if (!isset($_SESSION["student_id"]) || !isset($_SESSION["semester"])) {
    header("Location: index.php");
    exit();
}

$cookie_id = isset($_COOKIE["remember_student_id"])
    ? $_COOKIE["remember_student_id"]
    : "No cookie set";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration - Review</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h2>University Portal Registration</h2>
    <p class="step">Step 3 of 3 &mdash; Review &amp; Confirm</p>

    <div class="info">

        <p><strong>From PHP Session:</strong></p>
        <p>Student ID: <?php echo htmlspecialchars($_SESSION["student_id"]); ?></p>
        <p>Name: <?php echo htmlspecialchars($_SESSION["name"]); ?></p>
        <p>Email: <?php echo htmlspecialchars($_SESSION["email"]); ?></p>
        <p>Department: <?php echo htmlspecialchars($_SESSION["department"]); ?></p>
        <p>Semester: <?php echo htmlspecialchars($_SESSION["semester"]); ?></p>
        <p>Course: <?php echo htmlspecialchars($_SESSION["course"]); ?></p>
        <p>Credits: <?php echo htmlspecialchars($_SESSION["credits"]); ?></p>

    </div>

    <div class="info">

        <p><strong>From Browser Cookie:</strong></p>
        <p>Remembered Student ID: <?php echo htmlspecialchars($cookie_id); ?></p>

    </div>

    <a href="dashboard.php">Back</a>
    <a href="logout.php">Complete Registration</a>

</div>

</body>
</html>
