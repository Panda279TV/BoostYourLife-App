<?php
// Wenn man im Localhost ist, dann werden Fehler angezeigt, ansonsten nicht
if ($_SERVER["SERVER_ADDR"] === "127.0.0.1" || $_SERVER["SERVER_ADDR"] === "::1") {
    error_reporting(E_ALL);
    ini_set("display_errors", "1");
} else {
    error_reporting(E_ERROR);
    ini_set("display_errors", "0");
}
// Die Config Datei wird required, also reingeladen
require_once 'config.php';
// Session wird gestartet
session_start();

// ROOT_DIR Für Server / PHP Scripte
define("ROOT_DIR", dirname(__DIR__) . DIRECTORY_SEPARATOR);

// ROOT_URL Für Browser / Client / Header / Browser Adress Zeile
define("ROOT_URL", "http://localhost:8888/BoostYourLife-App/");

// Die Date Zeit im Code wird auf UTC gestellt
date_default_timezone_set("UTC");
// Die Klassen werden selbst required. Der Auto-Klassen-Loader
spl_autoload_register(function ($class) {
    require_once (ROOT_DIR . "classes/$class.php");
});
// Die Funktion Sanitize wird reingeladen, damit man sie überall hat
require_once ROOT_DIR . "/functions/sanitize.php";
// Die Funktion GetClientIp wird reingeladen, damit man sie überall hat
require_once ROOT_DIR . "/functions/getUserIP.php";
