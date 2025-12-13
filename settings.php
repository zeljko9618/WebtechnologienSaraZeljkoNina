<?php
require("start.php");

if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$currentUser = $_SESSION['user'];
$message = "";
$error = "";

$user = $service->loadUser($currentUser);
if (!$user) {
    $user = new Model\User($currentUser);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === 'save') {

    $user->setFirstName($_POST['firstName'] ?? '');
    $user->setLastName($_POST['lastName'] ?? '');
    $user->setCoffeOrTea($_POST['coffeeOrTea'] ?? '');
    $user->setDescription($_POST['description'] ?? '');

    $history = $user->getHistory();
    if (!is_array($history)) {
        $history = [];
    }
    $history[] = date('Y-m-d H:i:s');
    $user->setHistory($history);

    if ($service->saveUser($user)) {
        $message = "Profile saved successfully!";
        $user = $service->loadUser($currentUser);
    } else {
        $error = "Error while saving profile.";
    }
}

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
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
</head>

<body>
<div class="container mt-4">

  <h1 class="mb-4">Profile Settings</h1>

  <a href="friends.php" class="text-decoration-none">&lt; Back</a>

  <?php if ($message): ?>
    <div class="alert alert-success mt-3"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" class="mt-4">

    <!-- BASE DATA -->
    <h4 class="border-bottom pb-2 mb-3">Base Data</h4>

    <div class="mb-3">
      <input type="text" class="form-control"
             name="firstName"
             placeholder="First Name"
             value="<?= htmlspecialchars($firstName) ?>">
    </div>

    <div class="mb-3">
      <input type="text" class="form-control"
             name="lastName"
             placeholder="Last Name"
             value="<?= htmlspecialchars($lastName) ?>">
    </div>

    <div class="mb-4">
      <label class="form-label">Coffee or Tea?</label>
      <select class="form-select" name="coffeeOrTea">
        <option value="">Select...</option>
        <option value="Coffee" <?= $coffeeOrTea === 'Coffee' ? 'selected' : '' ?>>Coffee</option>
        <option value="Tea" <?= $coffeeOrTea === 'Tea' ? 'selected' : '' ?>>Tea</option>
        <option value="Neither" <?= $coffeeOrTea === 'Neither' ? 'selected' : '' ?>>Neither nor</option>
      </select>
    </div>

    <!-- DESCRIPTION -->
    <h4 class="border-bottom pb-2 mb-3">Tell Something About You</h4>

    <div class="mb-4">
      <textarea class="form-control"
                name="description"
                rows="4"
                placeholder="Short Description"><?= htmlspecialchars($description) ?></textarea>
    </div>

    <!-- CHAT LAYOUT -->
    <h4 class="border-bottom pb-2 mb-3">Preferred Chat Layout</h4>

    <div class="form-check mb-2">
      <input class="form-check-input" type="radio" name="layout" id="layout1" value="one-line">
      <label class="form-check-label" for="layout1">
        Username and message in one line
      </label>
    </div>

    <div class="form-check mb-4">
      <input class="form-check-input" type="radio" name="layout" id="layout2" value="two-lines">
      <label class="form-check-label" for="layout2">
        Username and message in separated lines
      </label>
    </div>

    <!-- BUTTONS -->
    <div class="d-flex">
      <a href="friends.php" class="btn btn-secondary flex-fill me-2">Cancel</a>
      <button type="submit"
              name="action"
              value="save"
              class="btn btn-primary flex-fill">
        Save
      </button>
    </div>

  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
