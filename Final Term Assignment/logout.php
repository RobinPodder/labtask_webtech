<?php

session_start();

$name = isset($_SESSION["name"]) ? $_SESSION["name"] : "Student";

// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Complete</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h2>Registration Successful</h2>

    <p class="success">
        Thank you, <?php echo htmlspecialchars($name); ?>.
        Your registration has been completed.
    </p>

    <p>
        Your session data has been cleared (session_unset + session_destroy).
        Your remembered Student ID cookie, if set, is still stored in the browser.
    </p>

    <a href="index.php">Register Another Student</a>

</div>

</body>
</html>
