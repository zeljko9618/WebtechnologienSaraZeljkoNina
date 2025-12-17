document.addEventListener("DOMContentLoaded", function () {

    const friendList  = document.getElementById("friend-list");
    const requestList = document.getElementById("request-list");
    const requestModal = new bootstrap.Modal(document.getElementById('requestModal'));

    function loadFriends() {
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {

            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    const friends = JSON.parse(xhr.responseText);

                    console.log("DEBUG → Friend-Daten vom Server:");
                    console.log(friends);

                    renderFriends(friends);
                } else {
                    console.error("Fehler beim Laden:", xhr.status, xhr.responseText);
                }
            }

        };

        xhr.open("GET", "ajax_load_friends.php", true);
        xhr.send();
    }

    function renderFriends(friends) {
        friendList.innerHTML = "";
        requestList.innerHTML = "";

        friends.forEach(f => {

            if (f.status === "accepted") {
                const link = document.createElement("a");
                link.href = "chat.php?friend=" + encodeURIComponent(f.username);
                link.className = "list-group-item list-group-item-action d-flex justify-content-between align-items-center";

                const name = document.createElement("span");
                name.textContent = f.username;
                link.appendChild(name);

                if (f.unread && f.unread > 0) {
                    const badge = document.createElement("span");
                    badge.className = "badge bg-primary rounded-pill";
                    badge.textContent = f.unread;
                    link.appendChild(badge);
                }

                friendList.appendChild(link);
            }

            if (f.status === "requested") {
                const item = document.createElement("div");
                item.className = "list-group-item";
                
                const textNode = document.createTextNode("Friend request from ");
                item.appendChild(textNode);
                
                const nameSpan = document.createElement("strong");
                nameSpan.textContent = f.username;
                nameSpan.style.cursor = "pointer";
                nameSpan.addEventListener("click", function() {
                    showRequestModal(f.username);
                });
                
                item.appendChild(nameSpan);
                requestList.appendChild(item);
            }
        });
    }

    function showRequestModal(username) {
        document.getElementById("requestModalText").textContent = 
            "Accept request?";
        document.getElementById("requestFriendName").value = username;
        document.getElementById("rejectFriendName").value = username;
        document.getElementById("requestModalLabel").textContent = "Request from " + username;
        requestModal.show();
    }

    loadFriends();
    setInterval(loadFriends, 1000);

});