<?php
require("start.php");

// Test Login
echo "<h2>Login Test</h2>";
var_dump($service->login("Tom", "12345678"));

// Test loadUser
echo "<h2>User laden</h2>";
$user = $service->loadUser("Tom");
var_dump($user);

// Test friends laden
echo "<h2>Freunde laden</h2>";
$friends = $service->loadFriends();
var_dump($friends);

// Test load messages
echo "<h2>Messages laden</h2>";
$messages = $service->loadMessages("Jerry");
var_dump($messages);

// Test friendExists
echo "<h2>User exists Test</h2>";
var_dump($service->userExists("Tom"));
var_dump($service->userExists("NonexistentUser123"));
