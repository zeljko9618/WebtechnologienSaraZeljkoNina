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
                link.className = "list-group-item list-group-item-action";

                let text = f.username;
                if (f.unread && f.unread > 0) {
                    text += " <span class='badge bg-primary ms-2'>" + f.unread + "</span>";
                }

                link.innerHTML = text;
                friendList.appendChild(link);
            }

            if (f.status === "requested") {
                const li = document.createElement("li");
                li.className = "list-group-item";

                const friendName = document.createElement("span");
                friendName.textContent = f.username;

                const button = document.createElement("button");
                button.type = "button";
                button.className = "btn btn-sm btn-primary ms-3";
                button.textContent = "Review";
                button.addEventListener("click", function() {
                    showRequestModal(f.username);
                });

                li.appendChild(friendName);
                li.appendChild(button);
                requestList.appendChild(li);
            }
        });
    }

    function showRequestModal(username) {
        document.getElementById("requestModalText").textContent = 
            "Do you want to accept the friend request from " + username + "?";
        document.getElementById("requestFriendName").value = username;
        document.getElementById("rejectFriendName").value = username;
        requestModal.show();
    }

    loadFriends();
    setInterval(loadFriends, 1000);

});