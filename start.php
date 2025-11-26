<?php

spl_autoload_register(function($class) {
    // Aus "Model\User" wird "Model/User.php" usw.
    include str_replace('\\', '/', $class) . '.php';
});

session_start();

// Konstanten
define('CHAT_SERVER_URL', 'https://online-lectures-cs.thi.de/chat/');
define('CHAT_SERVER_ID', '2be4aee9-c202-4213-ac5a-2ef0d47d9e35');

// BackendService einmalig erzeugen
$service = new Utils\BackendService(CHAT_SERVER_URL, CHAT_SERVER_ID);
