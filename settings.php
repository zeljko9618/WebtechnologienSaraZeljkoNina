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

// User laden
$user = $service->loadUser($currentUser);
if (!$user) {
    $user = new Model\User($currentUser);
}

// Formular verarbeiten
if (isset($_POST['action']) && $_POST['action'] === 'save') {
    // Alle Felder aus dem Formular holen
    $user->setFirstName($_POST['firstName'] ?? '');
    $user->setLastName($_POST['lastName'] ?? '');
    $user->setCoffeOrTea($_POST['coffeeOrTea'] ?? '');
    $user->setDescription($_POST['description'] ?? '');
    
    // History aktualisieren
    $history = $user->getHistory();
    if (!is_array($history)) {
        $history = array();
    }
    $history[] = date('Y-m-d H:i:s');
    $user->setHistory($history);
    
    // Speichern
    if ($service->saveUser($user)) {
        $message = "Profil erfolgreich gespeichert!";
        // User neu laden um aktuelle Daten anzuzeigen
        $user = $service->loadUser($currentUser);
    } else {
        $error = "Fehler beim Speichern des Profils.";
    }
}

// Werte für das Formular vorbereiten
$firstName = $user->getFirstName() ?? '';
$lastName = $user->getLastName() ?? '';
$coffeeOrTea = $user->getCoffeOrTea() ?? '';
$description = $user->getDescription() ?? '';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Profile Settings</title>
  <link rel="stylesheet" href="./style.css">
</head>

<body class="base-data">
  <h1>Profile Settings</h1>

  <p>
    <a href="friends.php">&lt; Back</a>
  </p>

  <?php if ($message): ?>
    <p style="color: green; text-align: center;"><?= htmlspecialchars($message); ?></p>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <p style="color: red; text-align: center;"><?= htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form action="settings.php" method="post">

    <fieldset>
      <legend>Base Data</legend>

      <label for="firstName">First Name</label>
      <input type="text" id="firstName" name="firstName" value="<?= htmlspecialchars($firstName); ?>"><br>

      <label for="lastName">Last Name</label>
      <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($lastName); ?>"><br>

      <label for="coffeeOrTea">Coffee or Tea</label>
      <select id="coffeeOrTea" name="coffeeOrTea">
        <option value="">Select...</option>
        <option value="Coffee" <?= $coffeeOrTea === 'Coffee' ? 'selected' : ''; ?>>Coffee</option>
        <option value="Tea" <?= $coffeeOrTea === 'Tea' ? 'selected' : ''; ?>>Tea</option>
        <option value="Neither" <?= $coffeeOrTea === 'Neither' ? 'selected' : ''; ?>>Neither</option>
      </select><br>
    </fieldset>

    <fieldset>
      <legend>Tell us about you</legend>
      <textarea id="description" name="description" placeholder="Describe yourself..."><?= htmlspecialchars($description); ?></textarea>
    </fieldset>

    <fieldset>
      <legend>Preferences</legend>
      <div class="chat-layout">
        <input type="radio" id="oneColumn" name="chatLayout" value="oneColumn">
        <label for="oneColumn">Username and message in one line</label><br>

        <input type="radio" id="twoColumns" name="chatLayout" value="twoColumns">
        <label for="twoColumns">Username and message in two separate lines</label><br>
      </div>
    </fieldset>

    <div class="buttons">
      <button class="gray" type="button" onclick="window.location.href='friends.php'">Cancel</button>
      <button class="blue" type="submit" name="action" value="save">Save</button>
    </div>

  </form>

</body>
</html>