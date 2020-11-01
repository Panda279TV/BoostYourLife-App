<?php
// Erstellt die Klasse redirect
class redirect
{
    // Macht Variablen Privat
    private $location;

    // Schaut ob es eine Nummer ist oder nicht und schickt die Weiterleitung genau da hin, wie man den Pfad geschrieben hat. Wenn der Pfad nicht gefunden wird, dann kommt er immer wieder zu Error Seite, wegen der htaccess
    public static function to($location)
    {
        if (is_numeric($location)) {
            header("Location: " . ROOT_URL . "includes/error/404.php");
        } elseif (empty($location)) {
            header("Location: " . ROOT_URL . "index.php");
        } else {
            header("Location: " . ROOT_URL . $location . ".php");
        }
    }
}
