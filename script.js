// ------------------------- FRIENDS.HTML -------------------------
  
// Constant Variables
const CURRENT_USER = "Tom";     //grad hard code, muss aber dynamisch sein
const URL_USERS = "https://online-lectures-cs.thi.de/chat/dummy/list-users";
const URL_FRIENDS = "https://online-lectures-cs.thi.de/chat/dummy/list-friends";
const URL_REQUEST = "https://online-lectures-cs.thi.de/chat/dummy/friend-request";
const URL_USER = "https://online-lectures-cs.thi.de/chat/146de835-e4ca-417f-87bc-9b33ca0a2c27/user";
const URL_FRIEND = "https://online-lectures-cs.thi.de/chat/a7478268-242b-45cf-88d1-c65785cf59f9/friend";
//const AUTH_TOKEN = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiVG9tIiwiaWF0IjoxNzYyNzg0NTAyfQ.5ejR1mIBfvjv5Bh91nDs4Jf5YZIzDBVp1rhyAUaafEs";

// Elemente im DOM
const friendsList = document.getElementById("users-list");
const requestsList = document.getElementById("requests-list");

let users = [];

// ------------- Dummy-Daten vom Server mit Hilfe von AJAX holen -------------
const xhttp = new XMLHttpRequest();

xhttp.open('GET', URL_USERS);
xhttp.onreadystatechange = function () {
  if (xhttp.readyState === 4 && xhttp.status === 200) {
    users = JSON.parse(xhttp.responseText);
    console.log(users); // zur Kontrolle in der Konsole

    // Zugriff auf die <datalist> im HTML
    const dataList = document.getElementById("friend-selector");
    // Zugriff auf die <ul> im HTML
    const usersList = document.getElementById("users-list");

    // Bereits vorhandene Usernamen aus <ul> sammeln
    const existingUsers = Array.from(usersList.querySelectorAll("li")).map(li => {
      // Nur den Namen nehmen, ohne <span>
      return li.childNodes[0].textContent.trim();
    });

    // Falls alte Einträge vorhanden sind, löschen
    dataList.innerHTML = "";

    // Users hinzufügen
    users.forEach(user => {
      if (user !== CURRENT_USER && !existingUsers.includes(user)) {
        const option = document.createElement("option");
        option.value = user;
        dataList.appendChild(option);
      }
    });
  }
};

xhttp.open("GET", URL_USER, true);    // Add token, e. g., from Tom
xhttp.setRequestHeader('Authorization', "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiVG9tIiwiaWF0IjoxNzYyNzg0NTAyfQ.5ejR1mIBfvjv5Bh91nDs4Jf5YZIzDBVp1rhyAUaafEs");
xhttp.send();

// ------------- Handhabung der Freundschaftsanfrage -------------

// onClick-Button Funktion, um eine Freundschaftsanfrage zu senden
function addFriend() {
  const input = document.getElementById("friend-request-name");
  const friendName = input.value.trim();

  // --- Prüfungen ---
  if (!friendName) {
    alert("Bitte einen Nutzernamen eingeben!");
    return;
  }

  if (!users.includes(friendName)) {
    alert("Dieser Nutzer existiert nicht!");
    return;
  }

  const existingUsers = Array.from(
    document.querySelectorAll("#users-list li")
  ).map(li => li.childNodes[0].textContent.trim());

  if (existingUsers.includes(friendName)) {
    alert("Dieser Nutzer ist bereits dein Freund!");
    return;
  }

  if (friendName === CURRENT_USER) {
    alert("Du kannst dich nicht selbst hinzufügen!");
    return;
  }

  // --- AJAX Request zum Senden der Freundschaftsanfrage ---
  let xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState === 4) {
      if (xmlhttp.status === 204) {
        console.log("Freundschaftsanfrage gesendet an:", friendName);
        alert(`Freundschaftsanfrage an ${friendName} gesendet!`);
        input.value = "";
      } else {
        console.error("Fehler beim Senden:", xmlhttp.status, xmlhttp.responseText);
        alert("Fehler beim Senden der Freundschaftsanfrage!");
      }
    }
  };

  xmlhttp.open("POST", "https://online-lectures-cs.thi.de/chat/a7478268-242b-45cf-88d1-c65785cf59f9/friend", true);
  xmlhttp.setRequestHeader("Content-type", "application/json");
  xmlhttp.setRequestHeader('Authorization', "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiVG9tIiwiaWF0IjoxNzYyNzg0NTAyfQ.5ejR1mIBfvjv5Bh91nDs4Jf5YZIzDBVp1rhyAUaafEs");
  // Der Server erwartet ein Objekt mit { username: "..." }
  let data = { username: friendName };
  let jsonString = JSON.stringify(data);
  xmlhttp.send(jsonString);
}


// loadFriends Funktion, welche jede Sekunde die Freundesliste neu lädt
function loadFriends() {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", URL_FRIENDS, true);
  xhr.setRequestHeader("Authorization", AUTH_TOKEN);

  xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {

          let response = JSON.parse(xhr.responseText);

          console.log("Empfangene Daten:", response);

          // Listen zuerst leeren
          friendsList.innerHTML = "";
          requestsList.innerHTML = "";

          response.forEach(friend => {

              // --- Freunde mit Status accepted ---
              if (friend.status === "accepted") {

                  const li = document.createElement("li");

                  // Link erzeugen
                  const a = document.createElement("a");
                  a.textContent = friend.username;
                  a.setAttribute("href", "chat.html?friend=" + friend.username);

                  li.appendChild(a);
                  friendsList.appendChild(li);
              }

              // --- Freundschaftsanfragen ---
              else if (friend.status === "requested") {

                  const li = document.createElement("li");
                  li.textContent = friend.username;
                  requestsList.appendChild(li);
              }
          });
      }
  };

  xhr.send();
}

// Jede Sekunde aktualisieren
window.setInterval(function () {
  loadFriends();
}, 1000);

// Erster Aufruf direkt
loadFriends();


// ------------------------- END FRIENDS.HTML -------------------------