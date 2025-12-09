<?php
spl_autoload_register(function ($class) {
    include str_replace("\\", "/", $class) . ".php";
});

// Start session
session_start();

// Backend Konstanten
define("CHAT_SERVER_URL", "https://online-lectures-cs.thi.de/chat/");
define("CHAT_SERVER_ID","39dc776c-17df-4889-824b-b664cc8142a3");

// BakckendService init
$service = new Utils\BackendService(CHAT_SERVER_URL, CHAT_SERVER_ID);
?>
