<?php
require "start.php";   // BackendService + Session wird hier gestartet

session_unset();        // Alle Session-Variablen löschen
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logout</title>
  <link rel="stylesheet" href="./style.css">
</head>

<body class="logged-out">
  <div class="logout-container">
    <img class="round" src="images/logout.png" width="100" height="100" alt="Logout Symbol">

    <h1>Logged out...</h1>

    <p class="bye-text">See u!</p>

    <p><a href="login.php" class="login-link">Login again</a></p>
  </div>
</body>
</html>
