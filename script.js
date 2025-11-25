// Backend Konfiguration 
const COLLECTION_ID = '2be4aee9-c202-4213-ac5a-2ef0d47d9e35';
const BACKEND_URL = `https://online-lectures-cs.thi.de/chat/${COLLECTION_ID}`;

// Tom's Token:
//const TOKEN = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiVG9tIiwiaWF0IjoxNzY0MTA0ODI1fQ.1Qqun7Cp0r1oBE12kH8cD14mHuVPalcxFfnQjI7984s';

// Jerry's Token:
const TOKEN = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiSmVycnkiLCJpYXQiOjE3NjQxMDQ4MjV9.xT50VStUSAlIBsZ8KOvUwnp-U4jJh58n6EJuxUkZEM4';

// Globale Variablen
let currentUser = null;
let allUsers = [];
let currentFriends = [];

// Backend Helper Funktionen mit XMLHttpRequest
function apiRequest(endpoint, method = 'GET', body = null) {
    return new Promise((resolve, reject) => {
        const xmlhttp = new XMLHttpRequest();
        
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                if (xmlhttp.status == 200 || xmlhttp.status == 204) {
                    if (xmlhttp.responseText) {
                        resolve(JSON.parse(xmlhttp.responseText));
                    } else {
                        resolve(null);
                    }
                } else {
                    reject(new Error(`HTTP error! status: ${xmlhttp.status}`));
                }
            }
        };
        
        xmlhttp.open(method, `${BACKEND_URL}${endpoint}`, true);
        xmlhttp.setRequestHeader('Authorization', `Bearer ${TOKEN}`);
        
        if (body) {
            xmlhttp.setRequestHeader('Content-Type', 'application/json');
            xmlhttp.send(JSON.stringify(body));
        } else {
            xmlhttp.send();
        }
    });
}

// Lädt alle verfügbaren Benutzer
async function loadUsers() {
    try {
        const users = await apiRequest('/user');
        // API gibt Array von Strings zurück
        allUsers = users.map(username => ({ username: username }));
        updateUserDatalist();
    } catch (error) {
        console.error('Fehler beim Laden der Benutzer:', error);
    }
}

// Aktualisiert die Datalist mit verfügbaren Benutzern
function updateUserDatalist() {
    const datalist = document.getElementById('friend-selector');
    datalist.innerHTML = '';
    
    // Filter für die Liste mit allen Benutzern
    const availableUsers = allUsers.filter(user => {
        // Prüfe, ob Benutzer bereits Freund ist
        const isFriend = currentFriends.some(friend => friend.username === user.username);
        // Prüfe, ob es der aktuelle Benutzer ist
        const isCurrentUser = currentUser && user.username === currentUser;
        
        return !isFriend && !isCurrentUser;
    });
    
    // Erstellt dynamisch die Auswahloptionen für die Datalist 
    availableUsers.forEach(user => {
        const option = document.createElement('option');    // Erstellt ein <option> Element
        option.value = user.username;                       // Setzt den Wert vom User welches von Backend geholt wurde
        datalist.appendChild(option);                       // Fügt es zur Datalist hinzu
    });
}

// Lädt die Freundesliste und Freundschaftsanfragen
async function loadFriends() {
    try {
        const friends = await apiRequest('/friend');
        
        // Filtere nach Status: accepted -> echte Freunde  / requested -> Anfragen, die erhalten wurden
        const acceptedFriends = friends.filter(f => f.status === 'accepted');
        const requestedFriends = friends.filter(f => f.status === 'requested');
        
        // Speichere aktuelle Freunde für Filter
        currentFriends = acceptedFriends;
        
        // Aktualisiere DOM
        updateFriendList(acceptedFriends);
        updateRequestList(requestedFriends);
        
        // Aktualisiere Datalist
        updateUserDatalist();
        
    } catch (error) {
        console.error('Fehler beim Laden der Freunde:', error);
    }
}

// Aktualisiert die Freundesliste im DOM
function updateFriendList(friends) {
    const friendList = document.getElementById('friend-list');
    friendList.innerHTML = '';
    
    friends.forEach(friend => {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = `chat.html?friend=${friend.username}`;
        a.textContent = friend.username;
        
        // Für später: Zeige ungelesene Nachrichten an
        if (friend.unread > 0) {
            a.textContent += ` (${friend.unread})`;
        }
        
        li.appendChild(a);
        friendList.appendChild(li);
    });
}

// Aktualisiert die Liste der Freundschaftsanfragen im DOM
function updateRequestList(requests) {
    const requestList = document.getElementById('request-list');
    requestList.innerHTML = '';
    
    if (requests.length === 0) {
        const li = document.createElement('li');
        li.textContent = 'No new requests';
        li.style.listStyle = 'none';
        requestList.appendChild(li);
        return;
    }
    
    requests.forEach(request => {
        const li = document.createElement('li');
        
        // Erstelle Text mit Benutzername
        const span = document.createElement('span');
        span.textContent = `Friend request from ${request.username} `;
        li.appendChild(span);
        
        // Accept Button
        const acceptBtn = document.createElement('button');
        acceptBtn.type = 'button';
        acceptBtn.textContent = 'Accept';
        acceptBtn.addEventListener('click', () => handleFriendRequest(request.username, 'accept'));
        li.appendChild(acceptBtn);
        
        // Reject Button
        const rejectBtn = document.createElement('button');
        rejectBtn.type = 'button';
        rejectBtn.textContent = 'Reject';
        rejectBtn.addEventListener('click', () => handleFriendRequest(request.username, 'reject'));
        li.appendChild(rejectBtn);
        
        requestList.appendChild(li);
    });
}

// Behandelt Freundschaftsanfragen (Accept/Reject)
async function handleFriendRequest(username, action) {
    try {
        // Wenn action === 'accept' -> TRUE: annehmen, wenn FALSE: dismiss
        const endpoint = action === 'accept' 
            ? `/friend/${username}/accept`
            : `/friend/${username}/dismiss`;
        
        // PUT Request für Accept/Reject
        await new Promise((resolve, reject) => {
            const xmlhttp = new XMLHttpRequest();
            
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status == 204 || xmlhttp.status == 200) {
                        resolve();
                    } else {
                        reject(new Error(`HTTP error! status: ${xmlhttp.status}`));
                    }
                }
            };
            
            xmlhttp.open('PUT', `${BACKEND_URL}${endpoint}`, true);
            xmlhttp.setRequestHeader('Authorization', `Bearer ${TOKEN}`);
            xmlhttp.setRequestHeader('Content-Type', 'application/json');
            xmlhttp.send();
        });
        
        // Aktualisierung der Liste
        await loadFriends();
        
    } catch (error) {
        console.error(`Fehler beim ${action} der Freundschaftsanfrage:`, error);
        alert(`Fehler beim ${action === 'accept' ? 'Akzeptieren' : 'Ablehnen'} der Anfrage`);
    }
}

// Fügt einen neuen Freund hinzu
async function addFriend() {
    const input = document.getElementById('friend-request-name');
    const username = input.value.trim();
    
    // Validierung
    if (!username) {
        input.style.border = '2px solid red';
        return;
    }
    
    // Prüfe ob Benutzer existiert
    const userExists = allUsers.some(user => user.username === username);
    if (!userExists) {
        input.style.border = '2px solid red';
        return;
    }
    
    // Prüfe ob aktuelle Benutzer
    if (currentUser && username === currentUser) {
        input.style.border = '2px solid red';
        return;
    }
    
    // Prüfe ob bereits Freund
    const isAlreadyFriend = currentFriends.some(friend => friend.username === username);
    if (isAlreadyFriend) {
        input.style.border = '2px solid red';
        return;
    }
    
    try {
        // Sende Freundschaftsanfrage
        await apiRequest('/friend', 'POST', { username: username });
        
        // Erfolgreich: Eingabefeld zurücksetzen
        input.value = '';
        input.style.border = '';
        
        // Aktualisierung
        await loadFriends();
        
    } catch (error) {
        console.error('Fehler beim Hinzufügen des Freundes:', error);
        input.style.border = '2px solid red';
    }
}

// Lädt den aktuellen Benutzer (aus dem Token)
function loadCurrentUser() {
    try {
        // Token dekodieren um Benutzernamen zu bekommen
        const tokenParts = TOKEN.split('.');
        if (tokenParts.length === 3) {
            const payload = JSON.parse(atob(tokenParts[1]));
            currentUser = payload.user;
            console.log('Aktueller Benutzer:', currentUser);
        }
    } catch (error) {
        console.error('Fehler beim Laden des aktuellen Benutzers:', error);
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', async () => {
    // Lade aktuellen Benutzer
    loadCurrentUser();
    
    // Lade initiale Daten
    await loadUsers();
    await loadFriends();
    
    // Starte periodische Aktualisierung (1 Sekunde)
    window.setInterval(() => {
        loadFriends();
    }, 1000);
    
    // Add Button Event Listener
    const addBtn = document.getElementById('add-friend-btn');
    addBtn.addEventListener('click', addFriend);
    
    // Enter-Taste im Input-Feld
    const input = document.getElementById('friend-request-name');
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addFriend();
        }
    });
    
    // Entferne roten Rahmen bei Eingabe
    input.addEventListener('input', () => {
        input.style.border = '';
    });
});