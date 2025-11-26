<!DOCTYPE html>
<html> 

<head>
  <meta charset="UTF-8">
  <title>Friends</title>
  <link rel="stylesheet" href="./style.css">
  <script src="script.js" defer></script>
</head>

<body class="friends-page">
  
  <h1>Friends</h1>

  <p>
    <a href="logout.php">&lt;Logout</a> |
    <a href="settings.php">Settings</a>
  </p>

  <ul id="friend-list">
    <!-- wird durch JS befüllt-->
  </ul>

  <hr>

  <h2>New Requests</h2>
  <ol id="request-list">
    <!-- wird durch JS befüllt-->
  </ol>

  <hr>

  <form action="friends.php" method="post">
    <input type="text" 
           id="friend-request-name" 
           name="friendRequestName" 
           placeholder="Add Friend to List"
           list="friend-selector">

    <datalist id="friend-selector">
      <!-- wird durch JS befüllt -->
    </datalist>
    
    <button type="button" id="add-friend-btn">Add</button>
  </form>

</body>

</html>