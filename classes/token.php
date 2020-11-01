<?php
// Erstellt die Klasse token
class token
{
    // Macht Variablen Privat
    private $randomString,
    $tokenString,
    $getToken,
    $tokenType,
    $tokenID;

    // Generiert einen Random String
    public function generateXSRFToken($length = 64)
    {
        // Der Function Code von "generateXSRFToken" wurde von Stackoverflow kopiert: https://stackoverflow.com/questions/4356289/php-random-string-generator
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?-';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    // Holt sich den Token aus der "token" Tabelle
    public function checkXSRFToken($tokenString)
    {
        $getToken = database::getConnections()->view("token", "tokenstring=:tokenstring", [':tokenstring' => $tokenString]);
        return $getToken;
    }
    // Löscht die Tokens aus der "token" Tabelle
    public function deleteXSRFToken($tokenID, $tokenType)
    {
        database::getConnections()->delete("token", "type=:type AND expires<NOW()", [":type" => $tokenType]);
        database::getConnections()->deleteByID("token", $tokenID);
    }
}
