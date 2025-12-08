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
    const confirmInput  = document.getElementById("passwordRepeat");

    const usernameError = document.getElementById("usernameError");
    const passwordError = document.getElementById("passwordError");
    const confirmError  = document.getElementById("passwordRepeatError");

    // ---------------------------
    //  USERNAME VALIDATION LIVE
    // ---------------------------
    function checkUsername() {
        const name = usernameInput.value.trim();

        if (name.length < 3) {
            markInvalid(usernameInput);
            usernameError.textContent = "Username must have at least 3 characters.";
            return false;
        }

        // AJAX check: does user exist?
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {

                // 204 = user exists
                if (xhr.status === 204) {
                    markInvalid(usernameInput);
                    usernameError.textContent = "Username already exists.";
                }

                // 404 = user does NOT exist → valid
                else if (xhr.status === 404) {
                    markValid(usernameInput);
                    usernameError.textContent = "";
                }
            }
        };

        xhr.open("GET", "ajax_check_user.php?user=" + encodeURIComponent(name), true);
        xhr.send();

        return true;
    }

    usernameInput.addEventListener("input", checkUsername);

    // ---------------------------
    //  PASSWORD VALIDATION
    // ---------------------------
    function checkPassword() {
        const pw = passwordInput.value;

        if (pw.length < 8) {
            markInvalid(passwordInput);
            passwordError.textContent = "Password must have at least 8 characters.";
            return false;
        }

        markValid(passwordInput);
        passwordError.textContent = "";
        return true;
    }

    function checkConfirm() {
        const pw = passwordInput.value;
        const pw2 = confirmInput.value;

        if (pw2 === "" || pw !== pw2) {
            markInvalid(confirmInput);
            confirmError.textContent = "Passwords do not match.";
            return false;
        }

        markValid(confirmInput);
        confirmError.textContent = "";
        return true;
    }

    passwordInput.addEventListener("input", () => {
        checkPassword();
        checkConfirm();
    });

    confirmInput.addEventListener("input", checkConfirm);

    // ---------------------------
    //  SUBMIT VALIDATION
    // ---------------------------
    form.addEventListener("submit", (e) => {

        let okUser = checkUsername();
        let okPw   = checkPassword();
        let okConf = checkConfirm();

        // Wenn ein Fehler → Formular NICHT absenden
        if (!okUser || !okPw || !okConf) {
            e.preventDefault();
            return;
        }

      
    });

});