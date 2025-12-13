<?php
require "start.php";

$usernameError = "";
$passwordError = "";
$confirmError  = "";

// Nur wenn Formular gesendet wurde
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["pw"] ?? "";
    $repeat   = $_POST["pwconfirmation"] ?? "";

    $valid = true;

    // ---- SERVERVALIDIERUNG ----

    // USERNAME
    if ($username === "" || strlen($username) < 3) {
        $usernameError = "Username must have at least 3 characters.";
        $valid = false;
    }
    elseif ($service->userExists($username)) {
        $usernameError = "Username already exists.";
        $valid = false;
    }

    // PASSWORT
    if ($password === "") {
        $passwordError = "Password must not be empty.";
        $valid = false;
    }
    elseif (strlen($password) < 8) {
        $passwordError = "Password must have at least 8 characters.";
        $valid = false;
    }

    // CONFIRM
    if ($password !== $repeat) {
        $confirmError = "Passwords do not match.";
        $valid = false;
    }

    // REGISTRIERUNG, wenn keine Fehler
    if ($valid) {

        $ok = $service->register($username, $password);

        if ($ok) {
            $_SESSION["user"] = $username;
            header("Location: friends.php");
            exit;
        } else {
            $usernameError = "Registration failed.";
        }
    }
}

// Standardwerte setzen, damit value="" im Formular funktioniert
$username = $username ?? "";
$repeat   = $repeat ?? "";

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register here!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
</head>

<body>
  <img class="round" src="images/user.png" width="90" height="90">
  <h1>Register yourself</h1>

<form id="registerForm" action="register.php" method="post">
    <div class="register">
      <fieldset>
        <legend>Register</legend>

        <!-- USERNAME -->
        <label for="username">Username</label>
        <input 
            type="text"
            id="username"
            name="username"
            value="<?= htmlspecialchars($username) ?>"
        >
        <p class="text-danger" id="usernameError">
            <?= $usernameError ?>
        </p>

        <!-- PASSWORD -->
        <label for="password">Password</label>
        <input 
            type="password" 
            id="password" 
            name="pw"
        >
        <p class="text-danger" id="passwordError">
            <?= $passwordError ?>
        </p>

        <!-- PASSWORD CONFIRMATION -->
        <label for="passwordRepeat">Confirm Password</label>
        <input 
            type="password" 
            id="passwordRepeat" 
            name="pwconfirmation"
        >
        <p class="error-message" id="passwordRepeatError">
            <?= $confirmError ?>
        </p>

      </fieldset>
    </div>

    <div class="buttons">
      <button type="button" class="btn btn-secondary" onclick="window.location.href='login.php'">
        Cancel
      </button>
      <button type="submit" class="btn btn-primary">
        Create Account
      </button>
    </div>
</form>
<script src="main.js"></script>
<script src="register.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
