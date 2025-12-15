<?php
require_once "start.php";

if (isset($_SESSION["user"])) {
    header("Location: friends.php");
    exit;
}

$usernameError = "";
$passwordError = "";
$loginError    = "";

$enteredUsername = $_POST["username"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "") {
        $usernameError = "Please enter a username.";
    }
    if ($password === "") {
        $passwordError = "Please enter a password.";
    }

    if ($username !== "" && $password !== "") {
        $ok = $service->login($username, $password);
        if ($ok) {
            $_SESSION["user"] = $username;
            header("Location: friends.php");
            exit;
        } else {
            $loginError = "Login failed. Please check username or password.";
        }
    }
}

$usernameClass = ($usernameError || $loginError) ? "is-invalid" : "";
$passwordClass = ($passwordError || $loginError) ? "is-invalid" : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

  <div class="container">
    <!-- Logo -->
    <div class="text-center mb-4">
      <img src="images/chat.png"
           width="120"
           height="120"
           alt="Chat Logo"
           class="rounded-circle">
    </div>

    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h4 class="text-center mb-4">Please sign in</h4>

            <?php if ($loginError): ?>
              <div class="alert alert-danger py-2">
                <?= htmlspecialchars($loginError) ?>
              </div>
            <?php endif; ?>

            <form action="login.php" method="post" novalidate>

              <!-- Username -->
              <div class="form-floating mb-3">
                <input
                  type="text"
                  class="form-control <?= $usernameClass ?>"
                  id="username"
                  name="username"
                  placeholder="Username"
                  value="<?= htmlspecialchars($enteredUsername) ?>"
                >
                <label for="username">Username</label>
                <?php if ($usernameError): ?>
                  <div class="invalid-feedback">
                    <?= htmlspecialchars($usernameError) ?>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Password -->
              <div class="form-floating mb-3">
                <input
                  type="password"
                  class="form-control <?= $passwordClass ?>"
                  id="password"
                  name="password"
                  placeholder="Password"
                >
                <label for="password">Password</label>
                <?php if ($passwordError): ?>
                  <div class="invalid-feedback">
                    <?= htmlspecialchars($passwordError) ?>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Buttons -->
              <div class="row g-2">
                <div class="col-6">
                  <button type="button"
                          class="btn btn-secondary w-100"
                          onclick="window.location.href='register.php'">
                    Register
                  </button>
                </div>
                <div class="col-6">
                  <button type="submit" class="btn btn-primary w-100">
                    Login
                  </button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
