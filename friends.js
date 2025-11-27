// friends.js

// Who am I? (must match the token in main.js: Tom or Jerry)
const currentUser = "Tom";

let allUsers = []; // ["Tom", "Jerry", ...]
let friends = [];  // [{username, status, unread}, ...]

document.addEventListener("DOMContentLoaded", function () {
    const friendInput = document.getElementById("friend-request-name");
    const friendDatalist = document.getElementById("friend-selector");
    const addButton = document.getElementById("add-friend-btn");
    const friendListElement = document.getElementById("friend-list");
    const requestListElement = document.getElementById("request-list");

	// Farbänderungs Funktionen für Inputs
    function markValid(input) {
        input.classList.remove("invalid");
        input.classList.add("valid");
    }

    function markInvalid(input) {
        input.classList.remove("valid");
        input.classList.add("invalid");
    }

    function loadUsers() {
        const xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                	//raphi: const friendSelectorFrag = document.createDocumentFragment();
                    allUsers = JSON.parse(xhr.responseText);
                    updateDatalist();
                } else {
                    console.error("Fehler beim Laden der Nutzer:", xhr.status, xhr.responseText);
                }
            }
        };

        xhr.open("GET", window.backendUrl + "/user", true);
        xhr.setRequestHeader("Authorization", "Bearer " + window.token);
        xhr.send();
    }

    function loadFriends() {
        const xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    friends = JSON.parse(xhr.responseText);
                    renderFriendLists();
                    updateDatalist(); // bereits existierende Freunde nicht anzeigen
                } else {
                    console.error("Fehler beim Laden der Freunde:", xhr.status, xhr.responseText);
                }
            }
        };

        xhr.open("GET", window.backendUrl + "/friend", true);
        xhr.setRequestHeader("Content-type", "application/json");
        xhr.setRequestHeader("Authorization", "Bearer " + window.token);
        xhr.send();
    }

    function updateDatalist() {
        friendDatalist.innerHTML = "";

        const blocked = new Set();
        blocked.add(currentUser);
        friends.forEach(f => blocked.add(f.username));

        allUsers
            .filter(name => !blocked.has(name))
            .forEach(name => {
                const option = document.createElement("option");
                option.value = name;
                friendDatalist.appendChild(option);
            });
    }

    function renderFriendLists() {
        friendListElement.innerHTML = "";
        requestListElement.innerHTML = "";
    
        friends.forEach(friend => {
            if (friend.status === "accepted") {
            	// Friend List:
                const li = document.createElement("li");
    
                // Remove Button (für extra UX)
    //             const removeBtn = document.createElement("button");
    //             removeBtn.textContent = "X";
    //             removeBtn.className = "button-ghost-danger";
    // 
    //             removeBtn.addEventListener("click", async (event) => {
    //                 event.preventDefault();
    //                 li.remove();
    // 
    //                 // Freund aus Backend entfernen (todo)
    //             });
    
                const link = document.createElement("a");
    
                if (friend.unread && friend.unread > 0) {
                    link.textContent = friend.username + " (" + friend.unread + ")";
                } else {
                    link.textContent = friend.username;
                }
    
                link.href = "chat.html?friend=" + encodeURIComponent(friend.username);
    
                // Button VOR dem Namen
                // li.appendChild(removeBtn);
                li.appendChild(link);
    
                friendListElement.appendChild(li);
    
            } else if (friend.status === "requested") {
                // Request List:
                const li = document.createElement("li");
    
                const textSpan = document.createElement("span");
                textSpan.innerHTML = 'Friend request from <b>' + friend.username + '</b>';
    
                const buttonsSpan = document.createElement("span");
    
                const acceptButton = document.createElement("button");
                acceptButton.textContent = "Accept";
    
                const rejectButton = document.createElement("button");
                rejectButton.textContent = "Reject";
                rejectButton.id = "danger-color-button";
    
                buttonsSpan.appendChild(acceptButton);
                buttonsSpan.appendChild(rejectButton);
    
                li.appendChild(textSpan);
                li.appendChild(buttonsSpan);
    
                requestListElement.appendChild(li);
            }
        });
    }

    function onAddFriend() {
        const name = friendInput.value.trim();

        // reset colors
        friendInput.classList.remove("valid", "invalid");

        // a) empty?
        if (name === "") {
            markInvalid(friendInput);
            return;
        }

        // b) user exists?
        if (!allUsers.includes(name)) {
            markInvalid(friendInput);
            alert("Dieser Benutzer existiert nicht.");
            return;
        }

        // c) not myself
        if (name === currentUser) {
            markInvalid(friendInput);
            alert("Du kannst dich nicht selbst hinzufügen.");
            return;
        }

        // d) not already friend / requested
        // const alreadyFriend = friends.some(f => f.username === name);
        // if (alreadyFriend) {
        //     markInvalid(friendInput);
        //     alert("Dieser Benutzer ist bereits in deiner Freundesliste oder angefragt.");
        //     return;
        // }

        // If we reach here: all checks ok -> send request
        const xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 204) {
                    friendInput.value = "";
                    markValid(friendInput);
                    loadFriends();
                } else {
                    markInvalid(friendInput);
                    console.error("Fehler bei Friend Request:", xhr.status, xhr.responseText);
                    alert("Fehler beim Senden der Freundschaftsanfrage.");
                }
            }
        };

        xhr.open("POST", window.backendUrl + "/friend", true);
        xhr.setRequestHeader("Content-type", "application/json");
        xhr.setRequestHeader("Authorization", "Bearer " + window.token);

        const data = { username: name };
        xhr.send(JSON.stringify(data));
    }

    // connect button to handler
    addButton.addEventListener("click", onAddFriend);

    // initial load
    loadUsers();
    loadFriends();

    window.setInterval(function() {
       	loadFriends();
    }, 1000);
});

