// === BACKEND KONFIGURATION ===
const backendUrl = "https://online-lectures-cs.thi.de/chat/90da5892-9ea4-43b8-814f-cc7f064555f1";

// === FORM ELEMENTE ===
const form = document.getElementById("registerForm");
const username = document.getElementById("username");
const password = document.getElementById("password");
const passwordRepeat = document.getElementById("passwordRepeat");

// === TEXT-ERROR FUNKTIONEN ===
function showError(id, message) {
  document.getElementById(id).innerText = message;
}

function clearError(id) {
  document.getElementById(id).innerText = "";
}

// === CSS-KLASSENWECHSEL ===
function setValid(input) {
  input.classList.remove("invalid");
  input.classList.add("valid");
}

function setInvalid(input) {
  input.classList.remove("valid");
  input.classList.add("invalid");
}

// === SERVERPRÜFUNG: USER EXISTIERT? ===
async function userExists(name) {
  if (!name || name.trim() === "") return false;

  const url = `${backendUrl}/user/${encodeURIComponent(name)}`;

  try {
    const response = await fetch(url);

    if (response.status === 204) {
      return true; // existiert
    }

    if (response.status === 404) {
      return false; // frei
    }

    console.error("Serverfehler:", response.status);
    return false;
  } catch (err) {
    console.error("Netzwerkfehler:", err);
    return false;
  }
}

// === VALIDIERUNG BEIM ABSENDEN ===
form.addEventListener("submit", async function (event) {
  event.preventDefault(); // erst stoppen — AJAX kommt später

  let hasError = false;
  const nameValue = username.value.trim();

  // Username Länge prüfen
  if (nameValue.length < 3) {
    setInvalid(username);
    showError("usernameError", "Username must have at least 3 characters.");
    hasError = true;
  } else {
    setValid(username);
    clearError("usernameError");
  }

  // Username bereits vergeben?
  const exists = await userExists(nameValue);
  if (exists) {
    setInvalid(username);
    showError("usernameError", "Username is already taken.");
    hasError = true;
  } else if (nameValue.length >= 3) {
    setValid(username);
    clearError("usernameError");
  }

  // Passwort Länge
  if (password.value.length < 8) {
    setInvalid(password);
    showError("passwordError", "Password must have at least 8 characters.");
    hasError = true;
  } else {
    setValid(password);
    clearError("passwordError");
  }

  // Passwort-Wiederholung
  if (password.value !== passwordRepeat.value || passwordRepeat.value === "") {
    setInvalid(passwordRepeat);
    showError("passwordRepeatError", "Passwords do not match.");
    hasError = true;
  } else {
    setValid(passwordRepeat);
    clearError("passwordRepeatError");
  }

  // Wenn Fehler → Formular NICHT absenden
  if (hasError) return;

  // Wenn keine Fehler → absenden (submit manuell)
  form.submit();
});
