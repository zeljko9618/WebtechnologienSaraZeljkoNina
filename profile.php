<?php
require("start.php");

// Prüfen ob User eingeloggt ist
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Prüfen ob ein Profilnutzer übergeben wurde
if (!isset($_GET['user']) || empty($_GET['user'])) {
    header("Location: friends.php");
    exit();
}

$profileUser = $_GET['user'];
$currentUser = $_SESSION['user'];

// User laden
$user = $service->loadUser($profileUser);
if (!$user) {
    header("Location: friends.php");
    exit();
}

// Werte vorbereiten
$firstName   = $user->getFirstName()   ?? 'N/A';
$lastName    = $user->getLastName()    ?? 'N/A';
$coffeeOrTea = $user->getCoffeOrTea()  ?? 'N/A';
$description = $user->getDescription() ?? 'No description available.';
$history     = $user->getHistory();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile of <?= htmlspecialchars($profileUser); ?></title>
  <link rel="stylesheet" href="./style.css">
</head>

<body class="profile-page">

  <h1>Profile of <?= htmlspecialchars($profileUser); ?></h1>

  <p>
    <a href="chat.php?friend=<?= urlencode($profileUser); ?>" class="friendslist">&lt; Back to Chat</a> |
    <a href="friends.php?action=remove-friend&friend=<?= urlencode($profileUser); ?>" class="remove-friend">Remove Friend</a>
  </p>

  <div class="profile-container">

    <div class="profile-left">
      <img src="images/user.png" alt="Profile Picture" class="profile-pic">
    </div>

    <div class="profile-right">
      <fieldset class="friend">
        <legend>Base Data</legend>

        <p class="info-block">
          <b>First Name:</b><br>
          <?= htmlspecialchars($firstName); ?>
        </p>

        <p class="info-block">
          <b>Last Name:</b><br>
          <?= htmlspecialchars($lastName); ?>
        </p>

        <p class="info-block">
          <b>Coffee or Tea:</b><br>
          <?= htmlspecialchars($coffeeOrTea); ?>
        </p>

        <p class="info-block">
          <b>Description:</b><br>
          <?= nl2br(htmlspecialchars($description)); ?>
        </p>

        <?php if (is_array($history) && count($history) > 0): ?>
        <p class="info-block">
          <b>Profile Changes:</b><br>
          <ul>
            <?php foreach ($history as $timestamp): ?>
              <li><?= htmlspecialchars($timestamp); ?></li>
            <?php endforeach; ?>
          </ul>
        </p>
        <?php endif; ?>

      </fieldset>
    </div>

  </div>

</body>
</html>
