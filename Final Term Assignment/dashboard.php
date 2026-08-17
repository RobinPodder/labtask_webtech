<?php

session_start();

if (!isset($_SESSION["student_id"])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $_SESSION["semester"] = $_POST["semester"];
    $_SESSION["course"]   = $_POST["course"];
    $_SESSION["credits"]  = $_POST["credits"];

    header("Location: cookie.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration - Step 2</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h2>University Portal Registration</h2>
    <p class="step">Step 2 of 3 &mdash; Academic Information</p>

    <div class="info">
        <p>Registering as:</p>
        <strong><?php echo htmlspecialchars($_SESSION["name"]); ?></strong>
        (<?php echo htmlspecialchars($_SESSION["student_id"]); ?>)
        &mdash; <?php echo htmlspecialchars($_SESSION["department"]); ?>
    </div>

    <form method="POST">

        <label>Semester</label>
        <select name="semester" required>
            <option value="">-- Select Semester --</option>
            <option value="Spring 2027">Spring 2027</option>
            <option value="Summer 2027">Summer 2027</option>
            <option value="Fall 2027">Fall 2027</option>
        </select>

        <label>Course Selection</label>
        <select name="course" required>
            <option value="">-- Select Course --</option>
            <option value="CSE 401 - Capstone">CSE 401 - Capstone</option>
            <option value="CSE 415 - Machine Learning">CSE 415 - Machine Learning</option>
            <option value="CSE 422 - Web Technologies">CSE 422 - Web Technologies</option>
        </select>

        <label>Credit Hours</label>
        <input
            type="number"
            name="credits"
            min="1"
            max="21"
            required
        >

        <button type="submit">
            Next: Review &amp; Confirm
        </button>

    </form>

</div>

</body>
</html>
