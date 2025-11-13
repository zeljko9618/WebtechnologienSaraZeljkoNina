// Read out the chatpartner from the URL
function getChatPartner() {
  const url = new URL(window.location.href);
  const queryParams = url.searchParams;
  const friendValue = queryParams.get("friend");
  return friendValue;
}

// Set Title
const friend = getChatPartner();
document.querySelector("h1").innerText = "Chat with " + friend;

// Load messages
function loadMessages() {
  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState == 4) {
      if (xmlhttp.status == 200) {
        const messages = JSON.parse(xmlhttp.responseText);
        displayMessages(messages);
      } else {
        console.error("Error while loading:", xmlhttp.status);
      }
    }
  };

  xmlhttp.open("GET", `${backendUrl}/message/${friend}`, true);
  xmlhttp.setRequestHeader("Authorization", "Bearer " + token);
  xmlhttp.send();
}

// Display messages in DOM
function displayMessages(messages) {
  const box = document.querySelector(".chat-box");
  box.innerHTML = ""; // delete old messages

  messages.forEach(msg => {
    const div = document.createElement("div");
    div.classList.add("message");

    div.innerHTML = `
      <span class="sender">${msg.from}:</span>
      <span class="text">${msg.msg}</span>
      <span class="time">${new Date(msg.time * 1000).toLocaleTimeString()}</span>
    `;

    box.appendChild(div);
  });

  // Scroll ans Ende
  box.scrollTop = box.scrollHeight;
}

// Send message
function sendMessage() {
  const input = document.querySelector("input[name='newmessage']");
  const messageText = input.value.trim();
  if (!messageText) return;

  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState == 4) {
      if (xmlhttp.status == 204) {
        input.value = ""; // clear input
        loadMessages();   // reload immediately
      } else {
        console.error("Error sending message:", xmlhttp.status);
      }
    }
  };

  xmlhttp.open("POST", `${backendUrl}/message`, true);
  xmlhttp.setRequestHeader("Content-Type", "application/json");
  xmlhttp.setRequestHeader("Authorization", "Bearer " + token);

  const data = { message: messageText, to: friend };
  xmlhttp.send(JSON.stringify(data));
}

// Bind button click
document.querySelector(".chat-input button").addEventListener("click", sendMessage);

// Update every second
loadMessages();
setInterval(loadMessages, 1000);
