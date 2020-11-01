<?php
// Erstellt die Klasse user
class user
{
    // Macht Variablen Public und diese kann man dann ausgeben
    private $_SESSION,
            $_COOKIE;
    public $userId,
    $userFirstName,
    $userLastName,
    $userEmail,
    $rememberMe;

    // Speichert die Werte in die Variablen der Klasse
    public function __construct()
    {
        if (!isset($_SESSION["id"])) {
            $_SESSION["id"] = "";
        }

        $this->userId = $_SESSION["id"];

        if (!isset($_SESSION["firstname"])) {
            $_SESSION["firstname"] = "";
        }

        $this->userFirstName = $_SESSION["firstname"];

        if (!isset($_SESSION["lastname"])) {
            $_SESSION["lastname"] = "";
        }

        $this->userLastName = $_SESSION["lastname"];

        if (!isset($_SESSION["email"])) {
            $_SESSION["email"] = "";
        }

        $this->userEmail = $_SESSION["email"];

        if (!isset($_COOKIE['rememberMe'])) {
            $_COOKIE['rememberMe'] = "";
        }

        $this->rememberMe = $_COOKIE['rememberMe'];
    }
    // Schaut ob in der Klasse die Variablen nicht leer sind, wenn ja dann hat er sich schonmal angemeldet und nicht ausgeloggt. Nach dem Ausloggen werden alle Sessions gelöscht und dann wäre das Ergebnis leer also false
    public function is_loggedIn()
    {
        if (!empty($this->userId) && !empty($this->userFirstName) && !empty($this->userLastName) && !empty($this->userEmail)) {
            return true;
        }
    }
    // Löscht alle Sessions und loggt so den User aus
    public function logout()
    {
        unset($_SESSION);
        session_destroy();
        return true;
    }
}
