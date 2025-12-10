<?php
<<<<<<< HEAD
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
=======
require("start.php");

// Wenn bereits eingeloggt, direkt zu friends
if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    header("Location: friends.php");
    exit();
}

$error = "";

// Formular verarbeiten
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        if ($service->login($username, $password)) {
            $_SESSION['user'] = $username;
            header("Location: friends.php");
            exit();
        } else {
            $error = "Login fehlgeschlagen. Bitte überprüfen Sie Ihre Eingaben.";
        }
    } else {
        $error = "Bitte füllen Sie alle Felder aus.";
>>>>>>> origin/nina
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login</title>
<<<<<<< HEAD
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <img class="round" src="images/chat.png" width="90" height="90" alt="Chat Logo">
  <h1>Please sign in</h1>

=======
  <link rel="stylesheet" href="./style.css">
</head>

<body>
  <img class="round" src="images/chat.png" width="90" height="90">

  <h1>Please sign in</h1>

  <?php if ($error): ?>
    <p style="color: red; text-align: center;"><?= htmlspecialchars($error); ?></p>
  <?php endif; ?>

>>>>>>> origin/nina
  <form action="login.php" method="post">
    <div class="login">
      <fieldset>
        <legend>Login</legend>

<<<<<<< HEAD
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

=======
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"><br><br>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password"><br><br>
>>>>>>> origin/nina
      </fieldset>
    </div>

    <div class="buttons">
<<<<<<< HEAD
      <button class="gray" type="button" onclick="window.location.href='register.php'">Register</button>
      <button class="blue" type="submit">Login</button>
    </div>

=======
      <button class="gray" type="submit" formaction="register.php" formmethod="get">Register</button>
      <button class="blue" type="submit">Login</button>
    </div>
>>>>>>> origin/nina
  </form>
</body>
</html>