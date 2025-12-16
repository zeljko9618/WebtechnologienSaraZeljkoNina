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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>

<body class="chat-page">
  <div class="container mt-5">
    <div class="row">
      <div class="col-12">
        <h1 class="mb-3">Chat with <?= htmlspecialchars($chatPartner); ?></h1>

        <nav class="mb-4">
          <a href="friends.php" class="btn btn-outline-secondary btn-sm me-2">← Back</a>
          <a href="profile.php?user=<?= urlencode($chatPartner); ?>" class="btn btn-outline-secondary btn-sm me-2">Profile</a>
          <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#removeFriendModal">Remove Friend</button>
        </nav>

        <!-- Chatbox -->
        <div class="chat-box mb-4 p-3 border rounded" style="min-height: 300px; max-height: 500px; overflow-y: auto; background-color: #f8f9fa;"></div>

        <!-- Input mit Button-Gruppe -->
        <div class="input-group mb-4">
          <input type="text" class="form-control" name="newmessage" placeholder="Enter your message..." aria-label="Message">
          <button class="btn btn-outline-primary" type="button">Send Message</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL für "Remove Friend" Bestätigung -->
  <div class="modal fade" id="removeFriendModal" tabindex="-1" aria-labelledby="removeFriendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="removeFriendModalLabel">Remove Friend</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to remove <strong><?= htmlspecialchars($chatPartner); ?></strong> as a friend?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <a href="friends.php?action=remove-friend&friend=<?= urlencode($chatPartner); ?>" class="btn btn-danger">Remove</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="chat.js"></script>
</body>
</html>