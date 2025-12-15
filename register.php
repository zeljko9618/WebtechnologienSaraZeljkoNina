<?php
require "start.php";

$usernameError = "";
$passwordError = "";
$confirmError  = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["pw"] ?? "";
    $repeat   = $_POST["pwconfirmation"] ?? "";

    $valid = true;

    if ($username === "" || strlen($username) < 3) {
        $usernameError = "Username must have at least 3 characters.";
        $valid = false;
    } elseif ($service->userExists($username)) {
        $usernameError = "Username already exists.";
        $valid = false;
    }

    if ($password === "" || strlen($password) < 8) {
        $passwordError = "Password must have at least 8 characters.";
        $valid = false;
    }

    if ($password !== $repeat) {
        $confirmError = "Passwords do not match.";
        $valid = false;
    }

    if ($valid && $service->register($username, $password)) {
        $_SESSION["user"] = $username;
        header("Location: friends.php");
        exit;
    }
}

$usernameClass = $usernameError ? "is-invalid" : "";
$passwordClass = $passwordError ? "is-invalid" : "";
$confirmClass  = $confirmError  ? "is-invalid" : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

<div class="container">

  <!-- Logo -->
  <div class="text-center mb-4">
    <img src="images/user.png"
         width="120"
         height="120"
         alt="User"
         class="rounded-circle">
  </div>

  <div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">

      <div class="card shadow-sm">
        <div class="card-body p-4">

          <h4 class="text-center mb-4">Register yourself</h4>

          <form id="registerForm" method="post" novalidate>

            <!-- USERNAME -->
            <div class="form-floating mb-3">
              <input type="text"
                     class="form-control <?= $usernameClass ?>"
                     id="username"
                     name="username"
                     placeholder="Username"
                     value="<?= htmlspecialchars($username ?? "") ?>">
              <label for="username">Username</label>
              <div class="invalid-feedback" id="usernameError">
                <?= $usernameError ?>
              </div>
            </div>

            <!-- PASSWORD -->
            <div class="form-floating mb-3">
              <input type="password"
                     class="form-control <?= $passwordClass ?>"
                     id="password"
                     name="pw"
                     placeholder="Password">
              <label for="password">Password</label>
              <div class="invalid-feedback" id="passwordError">
                <?= $passwordError ?>
              </div>
            </div>

            <!-- CONFIRM -->
            <div class="form-floating mb-4">
              <input type="password"
                     class="form-control <?= $confirmClass ?>"
                     id="passwordRepeat"
                     name="pwconfirmation"
                     placeholder="Confirm Password">
              <label for="passwordRepeat">Confirm Password</label>
              <div class="invalid-feedback" id="passwordRepeatError">
                <?= $confirmError ?>
              </div>
            </div>

            <!-- BUTTONS -->
            <div class="row g-2">
              <div class="col-6">
                <a href="login.php" class="btn btn-secondary w-100">
                  Cancel
                </a>
              </div>
              <div class="col-6">
                <button type="submit" class="btn btn-primary w-100">
                  Create Account
                </button>
              </div>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<script src="register.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
