document.addEventListener("DOMContentLoaded", () => {

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

    // LIVE VALIDATION
    usernameInput.addEventListener("input", validateUsername);
    passwordInput.addEventListener("input", () => {
        validatePassword();
        validateConfirm();
    });
    confirmInput.addEventListener("input", validateConfirm);

    // --- SUBMIT ---
    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const uOK = validateUsername();
        const pOK = validatePassword();
        const cOK = validateConfirm();

        if (!uOK || !pOK || !cOK) return;

        // USERNAME EXISTS?
        const username = usernameInput.value.trim();
        const xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                
                console.log("STATUS:", xhr.status, "RESPONSE:", xhr.responseText);

                // EXISTIERT → ERROR
                if (xhr.status === 204) {
                    markInvalid(usernameInput);
                    usernameError.textContent = "Username existiert bereits.";
                    return;
                }

                // EXISTIERT NICHT → WEITERLEITEN
                if (xhr.status === 404) {
                    markValid(usernameInput);
                    usernameError.textContent = "";

                    window.location.href = "friends.html";   // <<--- DAS HIER IST DIE ANFORDERUNG
                    return;
                }

                if (xhr.status === 200) {
                    window.location.href = "friends.html";   // <<--- DAS HIER IST DIE ANFORDERUNG
                    return;
                }
            }
        };

        xhr.open("GET", window.backendUrl + "/user/" + encodeURIComponent(username), true);
        xhr.setRequestHeader("Authorization", "Bearer " + window.token);
        xhr.send();
    });

});
