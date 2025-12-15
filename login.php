<?php
require_once "start.php";

// Wenn Benutzer bereits eingeloggt → weiterleiten
if (isset($_SESSION["user"])) {
    header("Location: friends.php");
    exit;
}

// Fehlermeldungen
$usernameError = "";
$passwordError = "";
$loginError    = "";

// Werte im Formular erhalten
$enteredUsername = $_POST["username"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    // --- VALIDIERUNG ---
    if ($username === "") {
        $usernameError = "Please enter a username.";
    }

    if ($password === "") {
        $passwordError = "Please enter a password.";
    }

    // Nur login versuchen wenn beide Felder gefüllt
    if ($username !== "" && $password !== "") {

        // Versuche Login
        $ok = $service->login($username, $password);

        if ($ok) {
            $_SESSION["user"] = $username;
            header("Location: friends.php");
            exit;
        } else {
            // Fehler: Username nicht existent ODER Passwort falsch
            $loginError = "Login failed. Please check username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
</head>

<body>

  <img class="round" src="images/chat.png" width="90" height="90" alt="Chat Logo">
  <h1>Please sign in</h1>

  <form action="login.php" method="post">
    <div class="login">
      <fieldset>
        <legend>Login</legend>

        <!-- USERNAME -->
        <label for="username">Username</label>
        <input 
            type="text" 
            id="username" 
            name="username"
            value="<?php echo htmlspecialchars($enteredUsername); ?>"
            class="<?php echo $usernameError ? 'invalid' : ''; ?>"
        >
        <p class="error-message"><?php echo $usernameError; ?></p>

        <!-- PASSWORD -->
        <label for="password">Password</label>
        <input 
            type="password" 
            id="password" 
            name="password"
            class="<?php echo $passwordError ? 'invalid' : ''; ?>"
        >
        <p class="error-message"><?php echo $passwordError; ?></p>

        <!-- LOGIN FEHLER (Username falsch oder Passwort falsch) -->
        <?php if ($loginError): ?>
            <p class="error-message"><?php echo $loginError; ?></p>
        <?php endif; ?>

      </fieldset>
    </div>

    <div class="buttons">
      <button class="gray" type="button" onclick="window.location.href='register.php'">Register</button>
      <button class="blue" type="submit">Login</button>
    </div>

  </form>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>