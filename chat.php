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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
</head>

<body class="chat-page">
  <h1>Chat with <?= htmlspecialchars($chatPartner); ?></h1>

  <p>
    <a href="friends.php" class="friendslist">&lt;Back</a> |
    <a href="profile.php?user=<?= urlencode($chatPartner); ?>" class="profile">Profile</a> |
    <a href="friends.php?action=remove-friend&friend=<?= urlencode($chatPartner); ?>" class="remove-friend">Remove Friend</a>
  </p>

  <!-- Chatbox startet leer -->
  <div class="chat-box"></div>

  <div class="chat-input">
    <input type="text" name="newmessage" placeholder="New Message">
    <button type="button">Send</button>
  </div>

  <script>
    const chatPartner = "<?= htmlspecialchars($chatPartner); ?>";
    
    // Elemente aus dem HTML
    const sendBtn = document.querySelector(".chat-input button");
    const msgInput = document.querySelector(".chat-input input");
    const messageList = document.querySelector(".chat-box");

    // Senden mit Enter
    msgInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            const msg = msgInput.value.trim();
            if (msg !== "") {
                sendMessage(msg);
                msgInput.value = "";
            }
        }
    });

    // Send-Button
    sendBtn.addEventListener("click", (e) => {
        e.preventDefault();
        const msg = msgInput.value.trim();
        if (msg !== "") {
            sendMessage(msg);
            msgInput.value = "";
        }
    });

    // Send Message
    function sendMessage(message) {
        let xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 204) {
                loadMessages();
            }
        };

        xhr.open("POST", "ajax_send_message.php", true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        const payload = JSON.stringify({ msg: message, to: chatPartner });
        xhr.send(payload);
    }

    // Nachrichten laden
    function renderMessages(data) {
        messageList.innerHTML = "";

        data.forEach(d => {
            const name = d.from;
            const msg = d.msg;
            const time = new Date(d.time).toLocaleTimeString('de-DE', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });

            const wrapper = document.createElement("div");
            wrapper.className = "message";

            const senderSpan = document.createElement("span");
            senderSpan.className = "sender";
            senderSpan.textContent = name;

            const textSpan = document.createElement("span");
            textSpan.className = "text";
            textSpan.textContent = msg;

            const timeSpan = document.createElement("span");
            timeSpan.className = "time";
            timeSpan.textContent = time;

            wrapper.appendChild(senderSpan);
            wrapper.appendChild(textSpan);
            wrapper.appendChild(timeSpan);

            messageList.appendChild(wrapper);
        });
    }

    function loadMessages() {
        const xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    renderMessages(data);
                } else {
                    console.error("Fehler beim Laden der Nachrichten:", xhr.status);
                }
            }
        };

        xhr.open("GET", "ajax_load_messages.php?to=" + encodeURIComponent(chatPartner), true);
        xhr.send();
    }

    // Auto-Refresh
    setInterval(loadMessages, 1000);

    // Initial load
    loadMessages();
  </script>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</html>