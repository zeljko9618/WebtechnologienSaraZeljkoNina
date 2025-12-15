<?php
require "start.php";
session_unset();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logout</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

  <div class="container">

  <div class="text-center mb-4">
      <img src="images/logout.png"
           width="140"
           height="140"
           alt="Logout Symbol"
           class="rounded-circle">
    </div>

    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card shadow-sm">
          <div class="card-body p-4 text-center">
            <h4 class="mb-2">Logged out...</h4>

            <p class="text-dark mb-4">See u!</p>

            <a class="btn btn-secondary w-100" href="login.php">
              Login again
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
