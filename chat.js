// -----------------------------
// CHAT HEADER
// -----------------------------
function getChatpartner() {
    const url = new URL(window.location.href);
    return url.searchParams.get("friend");
}

document.addEventListener("DOMContentLoaded", () => {
    const chatHeader = document.querySelector("body.chat-page h1");
    if (chatHeader) {
        chatHeader.textContent = "Chat with " + getChatpartner();
    }
});

// -----------------------------
// ELEMENTE AUS DEM HTML
// -----------------------------

// HTML hat KEINE Sonder-IDs → wir greifen über Klassen zu:
const sendBtn = document.querySelector(".chat-input button");

// HTML hat KEIN id="message" → Eingabe ist das .chat-input input:
const msgInput = document.querySelector(".chat-input input");

// Nachrichtenausgabe = .chat-box
const messageList = document.querySelector(".chat-box");

const friend = getChatpartner();


// -----------------------------
// SENDEN MIT ENTER
// -----------------------------
msgInput.addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
        event.preventDefault();
        const msg = msgInput.value.trim();
        if (msg !== "") {
            sendMessage(msg, friend);
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
        sendMessage(msg, friend);
        msgInput.value = "";
    }
});

// -----------------------------
// SEND MESSAGE
// -----------------------------
function sendMessage(message, receiver) {
    let xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 204) {
            loadMessages();
        }
    };

    xhr.open("POST", window.backendUrl + "/message", true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader("Authorization", "Bearer " + window.token);

    const payload = JSON.stringify({ message, to: receiver });
    xhr.send(payload);
}


// -----------------------------
// NACHRICHTEN LADEN
// -----------------------------
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

    xhr.open("GET", window.backendUrl + "/message/" + encodeURIComponent(friend), true);
    xhr.setRequestHeader("Authorization", "Bearer " + window.token);
    xhr.send();
}


// -----------------------------
// AUTO-REFRESH
// -----------------------------
setInterval(loadMessages, 1000);

// Initial load
loadMessages();
