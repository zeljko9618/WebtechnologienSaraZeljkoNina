<?php
require "start.php";

// Prüfen, ob der Nutzer eingeloggt ist
if (!isset($_SESSION["user"]) || empty($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

$currentUser = $_SESSION["user"];
$error = "";  // Fehlermeldung für Add Friend

// ------------------------------------
// OPTIONAL: Freund entfernen (GET)
// ------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'remove-friend' && isset($_GET['friend'])) {
    $friendName = $_GET['friend'];
    $service->friendDismiss($friendName); // gemäß Backend: dismiss = entfernen/ablehnen
    header("Location: friends.php");
    exit;
}

// ------------------------------------
// POST-Aktionen
// ------------------------------------
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

        // 1. Leer?
        if ($newFriend === "") {
            $error = "Please enter a username.";
        }
        // 2. Selbst hinzufügen verhindern
        elseif ($newFriend === $currentUser) {
            $error = "You cannot add yourself as a friend.";
        }
        // 3. Nutzer muss existieren
        elseif (!$service->userExists($newFriend)) {
            $error = "User '$newFriend' does not exist.";
        }
        else {
            // Anfrage senden
            $service->friendRequest(["username" => $newFriend]);

            header("Location: friends.php");
            exit;
        }
    }
}

// ------------------------------------
// FRIEND LISTE & ALLE USER LADEN
// ------------------------------------
$friends  = $service->loadFriends();
$allUsers = $service->loadUsers();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Friends of <?= htmlspecialchars($currentUser) ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="friends.js" defer></script>
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
                if (is_object($fr) && method_exists($fr, 'getUsername')) {
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

</body>
</html>
