<?php

session_start();

if (isset($_POST["login"])) {

    $username = trim($_POST["username"]);

    if ($username !== "") {

        $_SESSION["username"] = $username;

        if (isset($_POST["remember"])) {
            setcookie("remember_user", $username, time() + (7 * 24 * 60 * 60), "/");
        }

        header("Location: dashboard.php");
        exit();
    }
}

$rememberedUser = isset($_COOKIE["remember_user"]) ? $_COOKIE["remember_user"] : "";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Session and Cookie Demo</title>
    <link rel="stylesheet" href="style2.css">
</head>

<body>

<div class="container">

    <h2>Login</h2>

    <form method="POST">

        <label>Username</label>

        <input
            type="text"
            name="username"
            value="<?php echo htmlspecialchars($rememberedUser); ?>"
            required
        >

        <label>
            <input
                type="checkbox"
                name="remember"
                <?php echo $rememberedUser ? "checked" : ""; ?>
            >
            Remember Me
        </label>

        <button type="submit" name="login">
            Login
        </button>

    </form>

</div>

</body>
</html>
