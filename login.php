<?php
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
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="./style.css">
</head>

<body>
  <img class="round" src="images/chat.png" width="90" height="90">

  <h1>Please sign in</h1>

  <?php if ($error): ?>
    <p style="color: red; text-align: center;"><?= htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form action="login.php" method="post">
    <div class="login">
      <fieldset>
        <legend>Login</legend>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"><br><br>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password"><br><br>
      </fieldset>
    </div>

    <div class="buttons">
      <button class="gray" type="submit" formaction="register.php" formmethod="get">Register</button>
      <button class="blue" type="submit">Login</button>
    </div>
  </form>
</body>
</html>