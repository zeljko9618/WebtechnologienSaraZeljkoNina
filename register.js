function markValid(input) {
    input.classList.remove("invalid");
    input.classList.add("valid");
}

function markInvalid(input) {
    input.classList.remove("valid");
    input.classList.add("invalid");
}

// Elemente holen
const form = document.getElementById("registerForm");
const usernameInput = document.getElementById("username");
const passwordInput = document.getElementById("password");
const confirmInput  = document.getElementById("passwordRepeat");

const usernameError = document.getElementById("usernameError");
const passwordError = document.getElementById("passwordError");
const confirmError  = document.getElementById("passwordRepeatError");

// -------------------------------
// LIVE VALIDATION FUNCTIONS
// -------------------------------
function validateUsername() {
    const username = usernameInput.value.trim();

    if (username.length < 3) {
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

function validateConfirmPassword() {
    const pw = passwordInput.value;
    const confirm = confirmInput.value;

    if (pw !== confirm || confirm === "") {
        markInvalid(confirmInput);
        confirmError.textContent = "Passwörter stimmen nicht überein.";
        return false;
    }

    markValid(confirmInput);
    confirmError.textContent = "";
    return true;
}

// -------------------------------
// LIVE EVENT LISTENERS
// -------------------------------
usernameInput.addEventListener("input", validateUsername);
passwordInput.addEventListener("input", () => {
    validatePassword();
    validateConfirmPassword();
});
confirmInput.addEventListener("input", validateConfirmPassword);

// -------------------------------
// SUBMIT EVENT (inkl. API Check)
// -------------------------------
form.addEventListener("submit", function (event) {
    event.preventDefault();

    const uOK = validateUsername();
    const pOK = validatePassword();
    const cOK = validateConfirmPassword();

    if (!uOK || !pOK || !cOK) return;

    // USERNAME VIA API CHECKEN
    const username = usernameInput.value.trim();
    const xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            // USER EXISTIERT SCHON
            if (xhr.status === 204) {
                markInvalid(usernameInput);
                usernameError.textContent = "Username existiert bereits.";
                return;
            }

            // USER EXISTIERT NICHT
            if (xhr.status === 404) {
                markValid(usernameInput);
                usernameError.textContent = "";

                alert("Account erfolgreich erstellt!");
                window.location.href = "friends.html";
                return;
            }

            alert("Fehler beim Überprüfen des Usernames!");
        }
    };

    xhr.open("GET", window.backendUrl + "/user/" + encodeURIComponent(username), true);
    xhr.setRequestHeader("Authorization", "Bearer " + window.token);
    xhr.send();
});
