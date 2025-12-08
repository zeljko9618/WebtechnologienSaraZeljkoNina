document.addEventListener("DOMContentLoaded", function () {

    const friendList  = document.getElementById("friend-list");
    const requestList = document.getElementById("request-list");

    function loadFriends() {
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {

            if (xhr.readyState === 4) {
    if (xhr.status === 200) {
        const friends = JSON.parse(xhr.responseText);

        console.log("DEBUG → Friend-Daten vom Server:");
        console.log(friends);  // ← HIER kommt es rein!

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
                const li = document.createElement("li");
                const a = document.createElement("a");

                a.href = "chat.php?friend=" + encodeURIComponent(f.username);
                a.textContent = f.username;

                if (f.unread && f.unread > 0) {
                    a.textContent += " (" + f.unread + ")";
                }

                li.appendChild(a);
                friendList.appendChild(li);
            }

            if (f.status === "requested") {
                const li = document.createElement("li");

                li.innerHTML = `
                    Friend request from <b>${f.username}</b>
                    <form method="post" action="friends.php" style="display:inline;">
                        <input type="hidden" name="action" value="accept">
                        <input type="hidden" name="friend" value="${f.username}">
                        <button type="submit">Accept</button>
                    </form>

                    <form method="post" action="friends.php" style="display:inline;">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="friend" value="${f.username}">
                        <button type="submit" id="danger-color-button">Reject</button>
                    </form>
                `;

                requestList.appendChild(li);
            }
        });
    }

    loadFriends();
    setInterval(loadFriends, 1000);

});