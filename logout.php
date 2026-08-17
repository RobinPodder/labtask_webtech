<?php

session_start();

// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// Clear the "remember me" cookie
if (isset($_COOKIE["remember_user"])) {
    setcookie("remember_user", "", time() - 3600, "/");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <link rel="stylesheet" href="style2.css">
</head>

<body>

<div class="container">

    <h2>Logout Successful</h2>

    <p>
        The session has been destroyed.
    </p>

    <a href="se_co.php">Go to Login</a>

</div>

</body>
</html>
