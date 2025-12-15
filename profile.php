<?php
require("start.php");

if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['user']) || empty($_GET['user'])) {
    header("Location: friends.php");
    exit();
}

$profileUser = $_GET['user'];
$currentUser = $_SESSION['user'];

$user = $service->loadUser($profileUser);
if (!$user) {
    header("Location: friends.php");
    exit();
}

$firstName   = $user->getFirstName()   ?? 'N/A';
$lastName    = $user->getLastName()    ?? 'N/A';
$coffeeOrTea = $user->getCoffeOrTea()  ?? 'N/A';
$description = $user->getDescription() ?? 'No description available.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile of <?= htmlspecialchars($profileUser) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
</head>

<body>
<div class="container mt-4">

  <h1 class="mb-3">Profile of <?= htmlspecialchars($profileUser) ?></h1>

  <!-- ACTION BUTTONS -->
  <div class="mb-4">
    <a href="chat.php?friend=<?= urlencode($profileUser) ?>"
       class="btn btn-secondary me-2">
      &lt; Back to Chat
    </a>

    <button class="btn btn-danger"
            data-bs-toggle="modal"
            data-bs-target="#removeFriendModal">
      Remove Friend
    </button>
  </div>

  <!-- PROFILE CONTENT -->
  <div class="row">
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <img src="images/user.png"
               alt="Profile Picture"
               class="img-fluid mb-3"
               style="max-width:150px;">
        </div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card">
        <div class="card-body">

          <p><?= nl2br(htmlspecialchars($description)) ?></p>

          <table class="table mt-4">
            <tr>
              <th>Coffee or Tea?</th>
              <td><?= htmlspecialchars($coffeeOrTea) ?></td>
            </tr>
            <tr>
              <th>Name</th>
              <td><?= htmlspecialchars($firstName . ' ' . $lastName) ?></td>
            </tr>
          </table>

        </div>
      </div>
    </div>
  </div>

</div>

<!-- REMOVE FRIEND MODAL -->
<div class="modal fade" id="removeFriendModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Remove Friend</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        Are you sure you want to remove
        <strong><?= htmlspecialchars($profileUser) ?></strong>
        from your friends list?
      </div>

      <div class="modal-footer">
        <button type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal">
          Cancel
        </button>

        <a href="friends.php?action=remove-friend&friend=<?= urlencode($profileUser) ?>"
           class="btn btn-danger">
          Remove Friend
        </a>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
