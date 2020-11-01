<?php
// Die Funktion macht alle Leerzeichen, sowie Zeichen und Tags aus der $data raus und diese geht dann clean wieder weiter
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
