<?php
// Die Config Datei wird required, also reingeladen
require_once '../core/init.php';
// User ausloggen
$newUser = new user();
$newUser->logout();
// Weiterleiten zu der Startseite
redirect::to('index');
