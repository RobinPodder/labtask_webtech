<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $_SESSION["student_id"]  = $_POST["student_id"];
    $_SESSION["name"]        = $_POST["name"];
    $_SESSION["email"]       = $_POST["email"];
    $_SESSION["department"]  = $_POST["department"];

    if (isset($_POST["remember"])) {
        setcookie("remember_student_id", $_POST["student_id"], time() + (86400 * 30));
    }

    header("Location: dashboard.php");
    exit();
}

$saved_id = isset($_COOKIE["remember_student_id"]) ? $_COOKIE["remember_student_id"] : "";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration - Step 1</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h2>University Portal Registration</h2>
    <p class="step">Step 1 of 3 &mdash; Student Information</p>

    <form method="POST">

        <label>Student ID</label>
        <input
            type="text"
            name="student_id"
            value="<?php echo htmlspecialchars($saved_id); ?>"
            required
        >

        <label>Full Name</label>
        <input
            type="text"
            name="name"
            required
        >

        <label>Email</label>
        <input
            type="email"
            name="email"
            required
        >

        <label>Department</label>
        <select name="department" required>
            <option value="">-- Select Department --</option>
            <option value="CSE">CSE</option>
            <option value="EEE">EEE</option>
            <option value="BBA">BBA</option>
            <option value="Civil">Civil Engineering</option>
        </select>

        <label>
            <input
                type="checkbox"
                name="remember"
                <?php echo $saved_id ? "checked" : ""; ?>
            >
            Remember my Student ID on this browser
        </label>

        <button type="submit">
            Next: Academic Info
        </button>

    </form>

</div>

</body>
</html>
