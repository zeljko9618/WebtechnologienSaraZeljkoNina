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
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'save') {

    // Werte setzen
    $user->setFirstName($_POST['firstName'] ?? '');
    $user->setLastName($_POST['lastName'] ?? '');
    $user->setCoffeOrTea($_POST['coffeeOrTea'] ?? '');
    $user->setDescription($_POST['description'] ?? '');

    // Profiländerung zur History hinzufügen
    $history = $user->getHistory();
    if (!is_array($history)) {
        $history = [];
    }
    $history[] = date('Y-m-d H:i:s');
    $user->setHistory($history);

    // Speichern
    if ($service->saveUser($user)) {
        $message = "Profile saved successfully!";
        $user = $service->loadUser($currentUser);
    } else {
        $error = "Error while saving profile.";
    }
}

// Werte für Formular
$firstName   = $user->getFirstName() ?? "";
$lastName    = $user->getLastName() ?? "";
$coffeeOrTea = $user->getCoffeOrTea() ?? "";
$description = $user->getDescription() ?? "";
?>
<!DOCTYPE html>
<html lang="de">
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
    <p style="color: green; text-align:center;"><?= htmlspecialchars($message); ?></p>
  <?php endif; ?>

  <?php if ($error): ?>
    <p style="color: red; text-align:center;"><?= htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form action="settings.php" method="post">
    <fieldset>
      <legend>Base Data</legend>

      <label for="firstName">First Name</label>
      <input type="text" id="firstName" name="firstName" value="<?= htmlspecialchars($firstName); ?>"><br>

      <label for="lastName">Last Name</label>
      <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($lastName); ?>"><br>

      <label for="coffeeOrTea">Coffee or Tea?</label>
      <select id="coffeeOrTea" name="coffeeOrTea">
        <option value="">Select...</option>
        <option value="Coffee" <?= $coffeeOrTea === 'Coffee' ? 'selected' : ''; ?>>Coffee</option>
        <option value="Tea" <?= $coffeeOrTea === 'Tea' ? 'selected' : ''; ?>>Tea</option>
        <option value="Neither" <?= $coffeeOrTea === 'Neither' ? 'selected' : ''; ?>>Neither</option>
      </select>
    </fieldset>

    <fieldset>
      <legend>Tell Something About You</legend>
      <textarea id="description" name="description" rows="3" cols="40"><?= htmlspecialchars($description); ?></textarea>
    </fieldset>

    <fieldset class="chat-layout">
      <legend>Preferred Chat Layout</legend>

      <input type="radio" id="layout1" name="layout" value="one-line">
      <label for="layout1">Username and message in one line</label><br>

      <input type="radio" id="layout2" name="layout" value="two-lines">
      <label for="layout2">Username and message in separated lines</label><br>
    </fieldset>

    <div class="buttons">
      <button type="button" class="gray" onclick="window.location.href='friends.php'">Cancel</button>
      <button type="submit" class="blue" name="action" value="save">Save</button>
    </div>
  </form>

</body>
</html>
