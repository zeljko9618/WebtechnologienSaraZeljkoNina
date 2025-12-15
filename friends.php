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
    $service->removeFriend($friendName);   // <<--- WICHTIG: richtige Methode!
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
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="friends-page">

<h1>Friends of <?= htmlspecialchars($currentUser) ?></h1>

<p>
    <a href="logout.php">&lt;Logout</a> |
    <a href="settings.php">Settings</a>
</p>

<h2>Your Friends</h2>
<ul id="friend-list"></ul>

<hr>

<h2>Friend Requests</h2>
<ol id="request-list"></ol>

<hr>

<h2>Add Friend</h2>

<form action="friends.php" method="post">

    <input type="text" 
           id="friend-request-name" 
           name="friendRequestName"
           placeholder="Enter username..."
           list="friend-selector"
           autocomplete="off"
           value="<?= htmlspecialchars($_POST['friendRequestName'] ?? '') ?>">

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

    <button type="submit" name="action" value="add">Add</button>
</form>

<?php if (!empty($error)): ?>
    <p class="error-message"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
