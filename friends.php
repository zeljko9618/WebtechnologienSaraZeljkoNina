<?php
require("start.php");

// Prüfen ob User eingeloggt ist
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$currentUser = $_SESSION['user'];
$message = "";
$error = "";

// Freundschaftsanfragen annehmen/ablehnen
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'accept-friend' && isset($_POST['friend'])) {
        $friendName = $_POST['friend'];
        if ($service->friendAccept($friendName)) {
            $message = "Freundschaftsanfrage angenommen!";
        } else {
            $error = "Fehler beim Annehmen der Anfrage.";
        }
    } elseif ($_POST['action'] === 'reject-friend' && isset($_POST['friend'])) {
        $friendName = $_POST['friend'];
        if ($service->friendDismiss($friendName)) {
            $message = "Freundschaftsanfrage abgelehnt!";
        } else {
            $error = "Fehler beim Ablehnen der Anfrage.";
        }
    } elseif ($_POST['action'] === 'add-friend' && isset($_POST['friendRequestName'])) {
        $friendName = trim($_POST['friendRequestName']);
        if (!empty($friendName)) {
            if ($service->friendRequest(array("username" => $friendName))) {
                $message = "Freundschaftsanfrage gesendet!";
            } else {
                $error = "Fehler beim Senden der Anfrage.";
            }
        }
    }
}

// Freund entfernen (via GET)
if (isset($_GET['action']) && $_GET['action'] === 'remove-friend' && isset($_GET['friend'])) {
    $friendName = $_GET['friend'];
    if ($service->removeFriend($friendName)) {
        $message = "Freund entfernt!";
    } else {
        $error = "Fehler beim Entfernen des Freundes.";
    }
}

// Alle Nutzer laden (für Datalist)
$allUsers = $service->loadUsers();
if (!$allUsers) {
    $allUsers = array();
}

// Freunde laden
$friends = $service->loadFriends();
if (!$friends) {
    $friends = array();
}

// Ungelesene Nachrichten laden
$unread = $service->getUnread();
$unreadMap = array();
if ($unread) {
    foreach ($unread as $item) {
        $unreadMap[$item->username] = $item->unread;
    }
}

// Bereits befreundete Nutzer sammeln
$friendNames = array();
foreach ($friends as $friend) {
    $friendNames[] = $friend->getUsername();
}
?>
<!DOCTYPE html>
<html> 

<head>
  <meta charset="UTF-8">
  <title>Friends</title>
  <link rel="stylesheet" href="./style.css">
</head>

<body class="friends-page">
  
  <h1>Friends</h1>

  <p>
    <a href="logout.php">&lt;Logout</a> |
    <a href="settings.php">Settings</a>
  </p>

  <?php if ($message): ?>
    <p style="color: green;"><?= htmlspecialchars($message); ?></p>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <p style="color: red;"><?= htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <ul id="friend-list">
    <?php foreach ($friends as $friend): ?>
      <?php if ($friend->getStatus() === 'accepted'): ?>
        <li>
          <a href="chat.php?friend=<?= urlencode($friend->getUsername()); ?>">
            <?= htmlspecialchars($friend->getUsername()); ?>
            <?php if (isset($unreadMap[$friend->getUsername()]) && $unreadMap[$friend->getUsername()] > 0): ?>
              <span><?= $unreadMap[$friend->getUsername()]; ?></span>
            <?php endif; ?>
          </a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>

  <hr>

  <h2>New Requests</h2>
  <ol id="request-list">
    <?php foreach ($friends as $friend): ?>
      <?php if ($friend->getStatus() === 'requested'): ?>
        <li>
          Friend request from <b><?= htmlspecialchars($friend->getUsername()); ?></b>
          <form action="friends.php" method="post" style="display: inline;">
            <input type="hidden" name="friend" value="<?= htmlspecialchars($friend->getUsername()); ?>">
            <button type="submit" name="action" value="accept-friend">Accept</button>
            <button type="submit" name="action" value="reject-friend">Reject</button>
          </form>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ol>

  <hr>

  <form action="friends.php" method="post">
    <input type="text" 
           id="friend-request-name" 
           name="friendRequestName" 
           placeholder="Add Friend to List"
           list="friend-selector">

    <datalist id="friend-selector">
      <?php foreach ($allUsers as $username): ?>
        <?php if ($username !== $currentUser && !in_array($username, $friendNames)): ?>
          <option value="<?= htmlspecialchars($username); ?>">
        <?php endif; ?>
      <?php endforeach; ?>
    </datalist>
    
    <button type="submit" name="action" value="add-friend">Add</button>
  </form>

</body>

</html>