<?php
// Erstellt die Klasse session
class session
{
    // Macht Variablen Privat
    private $_SESSION,
    $session,
    $name,
    $value,
    $content;

    // Schaut ob die Session schon existiert und gibt true oder false aus
    public static function exists($name)
    {
        if (isset($_SESSION[$name])) {
            return true;
        } else {
            return false;
        }
    }
    // Speicher den value Wert in die Session ein oder Überschreibe diese
    public static function put($name, $value)
    {
        return $_SESSION[$name] = $value;
    }
    // Zeigt die Session an
    public static function get($name)
    {
        return $_SESSION[$name];
    }
    // Lösche die Session
    public static function delete($name)
    {
        if (self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }
    // Wenn die $_SESSION existiert, dann speichere sie in der $session Variable und lösche die $_SESSION und gebe die Variable aus. Wenn die $_SESSION nicht existert, dann schreibe sie neu
    // Sehr Praktisch, wenn man die $_SESSION nur einmal anzeigen lassen möchte
    public static function flash($name, $content = null)
    {
        if (self::exists($name)) {
            $session = self::get($name);
            self::delete($name);
            return $session;
        } else {
            self::put($name, $content);
        }
        return "";
    }
}
