<?php
require "start.php";

// Prüfen, ob der Nutzer eingeloggt ist
if (!isset($_SESSION["user"]) || empty($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

$currentUser = $_SESSION["user"];
$error = "";

// =========================================================
// 1) REMOVE FRIEND (GET)
// =========================================================
if (isset($_GET['action']) && $_GET['action'] === 'remove-friend' && isset($_GET['friend'])) {
    $friendName = $_GET['friend'];
    $service->removeFriend($friendName);
    header("Location: friends.php");
    exit;
}

// =========================================================
// 2) POST-Aktionen
// =========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action     = $_POST["action"] ?? "";
    $friendName = trim($_POST["friend"] ?? "");
    $newFriend  = trim($_POST["friendRequestName"] ?? "");

    // ACCEPT
    if ($action === "accept" && $friendName !== "") {
        $service->friendAccept($friendName);
        header("Location: friends.php");
        exit;
    }

    // REJECT
    if ($action === "reject" && $friendName !== "") {
        $service->friendDismiss($friendName);
        header("Location: friends.php");
        exit;
    }

    // ADD FRIEND
    if ($action === "add") {

        if ($newFriend === "") {
            $error = "Please enter a username.";
        }
        elseif ($newFriend === $currentUser) {
            $error = "You cannot add yourself as a friend.";
        }
        elseif (!$service->userExists($newFriend)) {
            $error = "User '$newFriend' does not exist.";
        }
        else {
            $service->friendRequest(["username" => $newFriend]);
            header("Location: friends.php");
            exit;
        }
    }
}

// =========================================================
// 3) FRIEND LISTE & ALLE USER LADEN
// =========================================================
$friends  = $service->loadFriends();
$allUsers = $service->loadUsers();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Friends of <?= htmlspecialchars($currentUser) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body class="friends-page">
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-3">Friends of <?= htmlspecialchars($currentUser) ?></h1>
                
                <nav class="mb-4">
                    <div class="btn-group" role="group">
                        <a href="logout.php" class="btn btn-secondary btn-sm">← Logout</a>
                        <a href="settings.php" class="btn btn-secondary btn-sm">Edit Profile</a>
                    </div>
                </nav>

                <!-- FRIENDS LIST -->
                <h2 class="mt-4 mb-3">Your Friends</h2>
                <div class="list-group mb-4" id="friend-list">
                    <!-- Wird durch JavaScript gefüllt -->
                </div>

                <!-- FRIEND REQUESTS -->
                <h2 class="mt-4 mb-3">Friend Requests</h2>
                <div class="list-group mb-4" id="request-list">
                    <!-- Wird durch JavaScript gefüllt -->
                </div>

                <!-- ADD FRIEND FORM -->
                <h2 class="mt-4 mb-3">Add Friend</h2>
                <form action="friends.php" method="post" class="mb-4">
                    <div class="input-group">
                        <input type="text" 
                               id="friend-request-name" 
                               name="friendRequestName"
                               class="form-control"
                               placeholder="Enter username..."
                               list="friend-selector"
                               autocomplete="off"
                               value="<?= htmlspecialchars($_POST['friendRequestName'] ?? '') ?>"
                               aria-label="Username">
                        <button class="btn btn-outline-primary" type="submit" name="action" value="add">Add</button>
                    </div>

                    <datalist id="friend-selector">
                    <?php
                        if (is_array($allUsers)) {
                            $blocked = [$currentUser];
                            if (is_array($friends)) {
                                foreach ($friends as $fr) {
                                    if ($fr instanceof Model\Friend) {
                                        $blocked[] = $fr->getUsername();
                                    }
                                }
                            }

                            foreach ($allUsers as $name) {
                                if (!is_string($name)) continue;
                                if (trim($name) === "") continue;
                                if (is_numeric($name)) continue;
                                if ($name === "undefined" || $name === "null") continue;
                                if (in_array($name, $blocked)) continue;
                                
                                echo "<option value='" . htmlspecialchars($name) . "'>";
                            }
                        }
                    ?>
                    </datalist>
                </form>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- MODAL für Freundschaftsanfragen -->
    <div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestModalLabel">Friend Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="requestModalText">Accept request?</p>
                </div>
                <div class="modal-footer">
                    <form id="rejectForm" method="post" action="friends.php" style="display:inline;">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="friend" id="rejectFriendName">
                        <button type="submit" class="btn btn-secondary">Dismiss</button>
                    </form>
                    <form id="acceptForm" method="post" action="friends.php" style="display:inline;">
                        <input type="hidden" name="action" value="accept">
                        <input type="hidden" name="friend" id="requestFriendName">
                        <button type="submit" class="btn btn-primary">Accept</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="friends.js"></script>
</body>
</html>