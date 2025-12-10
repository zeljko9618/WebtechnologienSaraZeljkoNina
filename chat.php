<?php
require("start.php");

// Prüfen ob User eingeloggt ist
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Prüfen ob Chat-Partner übergeben wurde
if (!isset($_GET['friend']) || empty($_GET['friend'])) {
    header("Location: friends.php");
    exit();
}

$chatPartner = $_GET['friend'];
$currentUser = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat</title>
  <link rel="stylesheet" href="./style.css">

  <!-- Deine JS-Files bleiben -->
  <script src="main.js"></script>
  <script src="chat.js" defer></script>
</head>

<body class="chat-page">

  <!-- Dein JS setzt diesen Header korrekt -->
  <h1>Chat with <?= htmlspecialchars($chatPartner); ?></h1>

  <p>
    <a href="friends.php" class="friendslist">&lt;Back</a> |
    <a href="profile.php?user=<?= urlencode($chatPartner); ?>" class="profile">Profile</a> |
    <a href="friends.php?action=remove-friend&friend=<?= urlencode($chatPartner); ?>" class="remove-friend">Remove Friend</a>
  </p>

  <!-- Chatbox -->
  <div class="chat-box"></div>

  <!-- Eingabe -->
  <div class="chat-input">
    <input type="text" name="newmessage" placeholder="New Message">
    <button type="button">Send</button>
  </div>

</body>
</html>
