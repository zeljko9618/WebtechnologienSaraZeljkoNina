document.addEventListener("DOMContentLoaded", () => {
    // Hole Chat-Partner aus URL
    const url = new URL(window.location.href);
    const chatPartner = url.searchParams.get("friend");

    // Elemente aus dem HTML
    const sendBtn = document.querySelector(".input-group button");
    const msgInput = document.querySelector(".input-group input");
    const messageList = document.querySelector(".chat-box");

    if (!sendBtn || !msgInput || !messageList || !chatPartner) {
        console.error("Chat-Elemente nicht gefunden – prüfe HTML-Struktur.");
        return;
    }

    // ========================================
    // SENDEN MIT ENTER
    // ========================================
    msgInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            const msg = msgInput.value.trim();
            if (msg !== "") {
                sendMessage(msg, chatPartner, messageList);
                msgInput.value = "";
                msgInput.focus();
            }
        }
    });

    // ========================================
    // SEND-BUTTON
    // ========================================
    sendBtn.addEventListener("click", (e) => {
        e.preventDefault();
        const msg = msgInput.value.trim();
        if (msg !== "") {
            sendMessage(msg, chatPartner, messageList);
            msgInput.value = "";
            msgInput.focus();
        }
    });

    // ========================================
    // AUTO-REFRESH
    // ========================================
    function refresh() {
        loadMessages(chatPartner, messageList);
    }

    setInterval(refresh, 1000);
    refresh(); // Initial laden
});

// ========================================
// SEND MESSAGE (über PHP-Proxy)
// ========================================
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

// ========================================
// NACHRICHTEN LADEN (über PHP-Proxy)
// ========================================
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
        wrapper.className = "message mb-2 p-2 bg-white rounded";

        const senderSpan = document.createElement("span");
        senderSpan.className = "sender fw-bold text-primary me-2";
        senderSpan.textContent = name;

        const textSpan = document.createElement("span");
        textSpan.className = "text";
        textSpan.textContent = msg;

        const timeSpan = document.createElement("span");
        timeSpan.className = "time text-muted ms-2";
        timeSpan.style.fontSize = "0.85rem";
        timeSpan.textContent = time;

        wrapper.appendChild(senderSpan);
        wrapper.appendChild(textSpan);
        wrapper.appendChild(timeSpan);

        messageList.appendChild(wrapper);
    });

    // Scroll to bottom
    messageList.scrollTop = messageList.scrollHeight;
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