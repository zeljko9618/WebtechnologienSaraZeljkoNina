document.addEventListener("DOMContentLoaded", () => {
    console.log("=== REGISTER.JS GESTARTET ===");
    console.log("Backend URL:", window.backendUrl);
    console.log("Token:", window.token);
    
    function markValid(input) {
        input.classList.remove("invalid");
        input.classList.add("valid");
    }
    
    function markInvalid(input) {
        input.classList.remove("valid");
        input.classList.add("invalid");
    }
    
    const form = document.getElementById("registerForm");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const confirmInput = document.getElementById("passwordRepeat");
    const usernameError = document.getElementById("usernameError");
    const passwordError = document.getElementById("passwordError");
    const confirmError = document.getElementById("passwordRepeatError");
    
    let checkTimeout = null;
    let usernameAvailable = false;
    
    // --- VALIDATION ---
    function validateUsername() {
        const name = usernameInput.value.trim();
        if (name.length < 3) {
            markInvalid(usernameInput);
            usernameError.textContent = "Username muss mindestens 3 Zeichen haben.";
            return false;
        }
        markValid(usernameInput);
        usernameError.textContent = "";
        return true;
    }
    
    function validatePassword() {
        const pw = passwordInput.value;
        if (pw.length < 8) {
            markInvalid(passwordInput);
            passwordError.textContent = "Passwort muss mindestens 8 Zeichen haben.";
            return false;
        }
        markValid(passwordInput);
        passwordError.textContent = "";
        return true;
    }
    
    function validateConfirm() {
        const pw = passwordInput.value;
        const conf = confirmInput.value;
        if (pw !== conf || conf === "") {
            markInvalid(confirmInput);
            confirmError.textContent = "Passwörter stimmen nicht überein.";
            return false;
        }
        markValid(confirmInput);
        confirmError.textContent = "";
        return true;
    }
    
    // LIVE-PRÜFUNG: Username Existenz
    function checkUsernameAvailability() {
        const username = usernameInput.value.trim();
        
        console.log(">>> Prüfe Username:", username);
        
        if (username.length < 3) {
            usernameAvailable = false;
            return;
        }
        
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                console.log(">>> API Status:", xhr.status);
                console.log(">>> API Response:", xhr.responseText);
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        // Wenn Response ein username-Feld hat UND es nicht leer ist = User existiert
                        if (response.username && response.username.trim() !== "") {
                            console.log(">>> USERNAME EXISTIERT BEREITS!");
                            usernameAvailable = false;
                            markInvalid(usernameInput);
                            usernameError.textContent = "Username existiert bereits.";
                        } 
                        // Leeres Objekt oder kein username = User existiert NICHT
                        else {
                            console.log(">>> Username verfügbar!");
                            usernameAvailable = true;
                            markValid(usernameInput);
                            usernameError.textContent = "";
                        }
                    } catch (e) {
                        console.log(">>> Parse Error:", e);
                        usernameAvailable = false;
                    }
                }
                // User existiert nicht - verfügbar!
                else if (xhr.status === 404) {
                    console.log(">>> Username verfügbar (404)!");
                    usernameAvailable = true;
                    markValid(usernameInput);
                    usernameError.textContent = "";
                }
                // 204 kann bedeuten: existiert (basierend auf Example Code)
                else if (xhr.status === 204) {
                    console.log(">>> USERNAME EXISTIERT BEREITS (204)!");
                    usernameAvailable = false;
                    markInvalid(usernameInput);
                    usernameError.textContent = "Username existiert bereits.";
                }
                else {
                    console.log(">>> Unerwarteter Status!");
                    usernameAvailable = false;
                }
            }
        };
        
        const url = window.backendUrl + "/user/" + encodeURIComponent(username);
        console.log(">>> GET Request zu:", url);
        
        xhr.open("GET", url, true);
        xhr.setRequestHeader("Authorization", "Bearer " + window.token);
        xhr.send();
    }
    
    // LIVE VALIDATION
    usernameInput.addEventListener("input", () => {
        console.log(">>> Input event");
        const username = usernameInput.value.trim();
        
        // Erst Basis-Validierung
        if (username.length < 3) {
            markInvalid(usernameInput);
            usernameError.textContent = "Username muss mindestens 3 Zeichen haben.";
            usernameAvailable = false;
            clearTimeout(checkTimeout);
            return;
        }
        
        // Wenn 3+ Zeichen: Prüfung starten
        clearTimeout(checkTimeout);
        checkTimeout = setTimeout(() => {
            checkUsernameAvailability();
        }, 500);
    });
    
    passwordInput.addEventListener("input", () => {
        validatePassword();
        validateConfirm();
    });
    
    confirmInput.addEventListener("input", validateConfirm);
    
    // --- SUBMIT ---
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        console.log("=== FORM SUBMIT ===");
        
        const uOK = validateUsername();
        const pOK = validatePassword();
        const cOK = validateConfirm();
        
        console.log("Validation:", {uOK, pOK, cOK});
        console.log("Username verfügbar?", usernameAvailable);
        
        if (!uOK || !pOK || !cOK) {
            console.log("Validation fehlgeschlagen!");
            return;
        }
        
        // Prüfe ob Username verfügbar ist
        if (!usernameAvailable) {
            console.log("Username nicht verfügbar!");
            markInvalid(usernameInput);
            usernameError.textContent = "Username existiert bereits.";
            return;
        }
        
        const username = usernameInput.value.trim();
        console.log("Registriere User:", username);
        
        // User registrieren
        const registerXhr = new XMLHttpRequest();
        registerXhr.onreadystatechange = function () {
            if (registerXhr.readyState === 4) {
                console.log("Register Status:", registerXhr.status);
                console.log("Register Response:", registerXhr.responseText);
                
                if (registerXhr.status === 204 || registerXhr.status === 201) {
                    console.log("Registrierung erfolgreich!");
                    window.location.href = "friends.html";
                } else {
                    console.log("Registrierung fehlgeschlagen!");
                    usernameError.textContent = "Registrierung fehlgeschlagen. Bitte versuchen Sie es erneut.";
                }
            }
        };
        
        const url = window.backendUrl + "/user";
        console.log("POST Request zu:", url);
        
        registerXhr.open("POST", url, true);
        registerXhr.setRequestHeader("Content-Type", "application/json");
        registerXhr.setRequestHeader("Authorization", "Bearer " + window.token);
        registerXhr.send(JSON.stringify({ username: username }));
    });
    
    console.log("=== EVENT LISTENERS REGISTRIERT ===");
});