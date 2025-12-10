// -----------------------------
// CHAT HELFER
// -----------------------------
function getChatpartner() {
    const url = new URL(window.location.href);
    return url.searchParams.get("friend");
}

document.addEventListener("DOMContentLoaded", () => {
    const friend = getChatpartner();

    // Header setzen (falls nötig)
    const chatHeader = document.querySelector("body.chat-page h1");
    if (chatHeader && friend) {
        chatHeader.textContent = "Chat with " + friend;
    }

    // Elemente aus dem HTML
    const sendBtn = document.querySelector(".chat-input button");
    const msgInput = document.querySelector(".chat-input input");
    const messageList = document.querySelector(".chat-box");

    if (!sendBtn || !msgInput || !messageList || !friend) {
        console.error("Chat-Elemente nicht gefunden – prüfe HTML-Struktur.");
        return;
    }

    // -----------------------------
    // SENDEN MIT ENTER
    // -----------------------------
    msgInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            const msg = msgInput.value.trim();
            if (msg !== "") {
                sendMessage(msg, friend, messageList);
                msgInput.value = "";
            }
        }
    });

    // -----------------------------
    // SEND-BUTTON
    // -----------------------------
    sendBtn.addEventListener("click", (e) => {
        e.preventDefault();
        const msg = msgInput.value.trim();
        if (msg !== "") {
            sendMessage(msg, friend, messageList);
            msgInput.value = "";
        }
    });

    // -----------------------------
    // AUTO-REFRESH
    // -----------------------------
    function refresh() {
        loadMessages(friend, messageList);
    }

    setInterval(refresh, 1000);
    refresh(); // Initial laden
});

// -----------------------------
// SEND MESSAGE (über PHP-Proxy)
// -----------------------------
function sendMessage(message, receiver, messageList) {
    let xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 204) {
                // nach erfolgreichem Senden neu laden
                loadMessages(receiver, messageList);
            } else {
                console.error("Fehler beim Senden der Nachricht:", xhr.status, xhr.responseText);
            }
        }
    };

    xhr.open("POST", "ajax_send_message.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");

    const payload = JSON.stringify({ msg: message, to: receiver });
    xhr.send(payload);
}

// -----------------------------
// NACHRICHTEN LADEN (über PHP-Proxy)
// -----------------------------
function renderMessages(data, messageList) {
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

function loadMessages(friend, messageList) {
    const xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    renderMessages(data, messageList);
                } catch (e) {
                    console.error("Fehler beim Parsen der Nachrichten:", e, xhr.responseText);
                }
            } else if (xhr.status === 404) {
                // keine Nachrichten – einfach leere Liste
                messageList.innerHTML = "";
            } else {
                console.error("Fehler beim Laden der Nachrichten:", xhr.status, xhr.responseText);
            }
        }
    };

    xhr.open("GET", "ajax_load_messages.php?to=" + encodeURIComponent(friend), true);
    xhr.send();
}
