<?php
// Der Code wurde von hier kopiert = https://www.lima-city.de/thread/ich-moechte-eine-ip-in-die-datenbank-schreiben
// Die Funktion versucht die Ip Adresse zu holen auf verschiedene $_SERVER weisen, so dass man immer eine IP bekommt und falls man dies nicht bekommt, dann immer Unknown
function get_client_ip()
{
    $ipAddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP']) {
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
    } else if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if ($_SERVER['HTTP_X_FORWARDED']) {
        $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
    } else if ($_SERVER['HTTP_FORWARDED_FOR']) {
        $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if ($_SERVER['HTTP_FORWARDED']) {
        $ipAddress = $_SERVER['HTTP_FORWARDED'];
    } else if ($_SERVER['REMOTE_ADDR']) {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipAddress = 'UNKNOWN';
    }

    return $ipAddress;
}
