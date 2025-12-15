document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("registerForm");

  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");
  const confirmInput  = document.getElementById("passwordRepeat");

  const usernameError = document.getElementById("usernameError");
  const passwordError = document.getElementById("passwordError");
  const confirmError  = document.getElementById("passwordRepeatError");

  function setValid(input, feedback) {
    input.classList.remove("is-invalid");
    input.classList.add("is-valid");
    if (feedback) feedback.textContent = "";
  }

  function setInvalid(input, feedback, msg) {
    input.classList.remove("is-valid");
    input.classList.add("is-invalid");
    if (feedback) feedback.textContent = msg;
  }

  function checkUsername() {
    const name = usernameInput.value.trim();

    if (name.length < 3) {
      setInvalid(usernameInput, usernameError, "Username must have at least 3 characters.");
      return false;
    }

    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4) {
        if (xhr.status === 204) {
          setInvalid(usernameInput, usernameError, "Username already exists.");
        } else if (xhr.status === 404) {
          setValid(usernameInput, usernameError);
        }
      }
    };

    xhr.open("GET", "ajax_check_user.php?user=" + encodeURIComponent(name), true);
    xhr.send();

    return true;
  }

  function checkPassword() {
    if (passwordInput.value.length < 8) {
      setInvalid(passwordInput, passwordError, "Password must have at least 8 characters.");
      return false;
    }
    setValid(passwordInput, passwordError);
    return true;
  }

  function checkConfirm() {
    if (confirmInput.value !== passwordInput.value || confirmInput.value === "") {
      setInvalid(confirmInput, confirmError, "Passwords do not match.");
      return false;
    }
    setValid(confirmInput, confirmError);
    return true;
  }

  usernameInput.addEventListener("input", checkUsername);
  passwordInput.addEventListener("input", () => { checkPassword(); checkConfirm(); });
  confirmInput.addEventListener("input", checkConfirm);

  form.addEventListener("submit", (e) => {
    if (!checkUsername() || !checkPassword() || !checkConfirm()) {
      e.preventDefault();
    }
  });

});
