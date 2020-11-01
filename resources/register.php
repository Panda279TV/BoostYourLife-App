<?php
// Die Config Datei wird required, also reingeladen
require_once '../core/init.php';
// Speichere die Userdaten Klasse in die Variable
$newUser = new user();

// Wenn der Cookie nicht existiert, dann mache ihn leer
if (!isset($_COOKIE['RememberMe'])) {
    $_COOKIE['RememberMe'] = "";
}

// Schaut nach ob der Cookie mit dem tokenstring übereinstimmt
$tokenData = database::getConnections()->view("token", "tokenstring=:tokenstring", [':tokenstring' => $_COOKIE['RememberMe']]);

// Wenn der Cookie nicht leer ist, dieser auch mit dem tokenstring übereinstimmt und noch der Type == "RememberMe" ist, dann mach weiter
if (!empty($_COOKIE['RememberMe']) && $tokenData == true && $tokenData["type"] == "RememberMeToken") {
    // Hole dir die Userid von dem Token und speichere sie in der $_SESSION
    session::put("id", $tokenData["userid"]);
    // Schaue nach dem User mit der Userid und danach speichere Informationen in weiteren $_SESSION
    $data = database::getConnections()->view("userdata", "id=:id", [':id' => $_SESSION["id"]]);
    session::put("firstname", $data["firstname"]);
    session::put("lastname", $data["lastname"]);
    session::put("email", $data["email"]);
    // Logge ihn durch die RememberMe Funktion ein
    redirect::to('resources/home');
} elseif ($newUser->is_loggedIn() == true) {
    // Wenn der User sich nicht ausloggt und so nicht die session_start() zerstört wird, dann kann wird er weitergeleitet und sozusagen automatisch eingeloggt
    redirect::to('resources/home');
}

// Speichere alle Variablen als leer
$error = $errorFirstName = $errorLastName = $errorEmail = $errorPassword = $errorPasswordRepeat = $errorAGBCheckbox = $firstnameInput = $lastnameInput = $emailInput = $passwordInput = $passwordRepeatInput = $checkboxInput = $registrationValidation = "";

// Wenn die $_POSTS und $_SESSION nicht existieren, dann mache sie leer
if (!isset($_POST['xsrfToken'])) {
    $_POST['xsrfToken'] = "";
}

if (!isset($_POST['agbCheckboxRegister'])) {
    $_POST['agbCheckboxRegister'] = "";
}

if (!isset($_SESSION['registerCount'])) {
    $_SESSION['registerCount'] = 0;
}

// Ist die $_SESSION['registerCount'] auf 4, dann logge den User aus und schicke ihn auf die Error Seite
if ($_SESSION['registerCount'] > 3) {
    $newUser->logout();
    redirect::to('404');
}

// Speichere die Token Klasse in die Variable
$tokenClass = new token();
$xsrfType = "RegisterForm";
$xsrfExpires = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +5 minutes"));

// Wenn der Server Request Method == "Post", dann mach bitte weiter
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Speichere alle eingaben in Variablen und filter diese direkt
    $firstnameInput = sanitize_input(ucfirst($_POST['firstname']));
    $lastnameInput = sanitize_input(ucfirst($_POST['lastname']));
    $emailInput = sanitize_input($_POST['email']);
    $passwordInput = sanitize_input($_POST['password']);
    $passwordRepeatInput = sanitize_input($_POST['passwordRepeat']);
    $checkboxInput = $_POST['agbCheckbox'];

    // Wenn nichts eingegeben wurde, dann führe den Fehler aus
    if (empty($firstnameInput) && empty($lastnameInput) && empty($emailInput) && empty($passwordInput) && empty($passwordRepeatInput)) {
        $errorRegister = '<p class="errorMessages">Bitte geben Sie etwas in die Felder ein!</p>';
    } else {
        // Speichere die Validation Klasse in die Variable
        $validation = new validation();
        // Wenn was eingegeben wurde, dann validiere es
        $registrationValidation = $validation->validRegister($firstnameInput, $lastnameInput, $emailInput, $passwordInput, $passwordRepeatInput, $checkboxInput);
    }

    // Wenn die Validierung erfolgreich geklappt hat, mache weiter
    if ($registrationValidation == true) {
        // Schaue ob es die E-Mail schon mal in der Dtaenbank vorhanden ist
        $data = database::getConnections()->view("userdata", "email=:email", [':email' => $emailInput]);
        // Gibt es die E-Mail nicht, dann gehe weiter
        if ($data == false) {
            // Schau ob es den Token in der Datenbank gibt und wenn ja dann mach weiter
            if (($tokenData = $tokenClass->checkXSRFToken($_POST["xsrfToken"])) == true) {
                // Hashe das Passwort und speichere es in einer neuen Variable
                $password_hash = password_hash($passwordInput, PASSWORD_DEFAULT);
                // Speichere die Ip Adresse
                $ipAddress = get_client_ip();

                // Die ganzen Daten werden eingespeichert und es wird geschaut ob es geklappt hat, wenn ja oder nein, zeige Fehler an
                if (database::getConnections()->insertRegisterUserData("userdata", $firstnameInput, $lastnameInput, $emailInput, $password_hash) == true) {
                    $data = database::getConnections()->view("userdata", "email=:email", [':email' => $emailInput]);
                    if (database::getConnections()->insertRegisterUserInfo("userinfo", $data["id"], $ipAddress) == true) {
                        $errorRegister = '<p class="success">Sie wurden erfolgreich registriert!</p>';
                    }
                } else {
                    $errorRegister = '<p class="error">Beim Anlegen Ihres Accounts ist ein fehler aufgetreten! Versuchen Sie es erneut!</p>';
                }
                // Lösche alle Tokens, die den gleichen Type haben und abgelaufen sind
                $tokenClass->deleteXSRFToken($tokenData["id"], $xsrfType);

                // Es wird ein Token erstellt zum Aktivieren des Accounts, dieser wird auch hochgeladen in die "token" Tabelle
                $xsrfTypeRegisterActivate = "RegisterAccountActivate";
                $xsrfExpiresRegisterActivate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +1440 minutes")); // 24 Stunden (1 Tage)
                $xsrfTokenRegisterActivate = $tokenClass->generateXSRFToken();
                database::getConnections()->tokenInsert($xsrfTokenRegisterActivate, $xsrfTypeRegisterActivate, $xsrfExpiresRegisterActivate, $data["id"]);

                // Schreibe dem User eine E-Mail mit allen Registrierungsdaten und dem Aktivierungslink
                $newMail = new mail();
                $newMail->registerAccountDataMail($emailInput, $firstnameInput, $lastnameInput, $ipAddress, $xsrfTokenRegisterActivate, $data["id"]);

                // 1 Sekunde abwarten und dann weiter machen
                sleep(1);

                // ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //
                // Diese zwei Links hier unten drunter sind nur dafür da, für die Entwicklung, also wenn man keine E-Mail enthalten hat oder kann
                echo '<p class="success">Sie haben sich erfolgreich registriert und haben dadurch eine E-Mail erhalten! Bitte schauen Sie auch im Spam Ordner nach! Diese E-Mail kann 1-20 Minuten brauchen. Falls Sie nach 20 Minuten keine E-Mail erhalten haben, wenden Sie sich bitte an den Support ===> support@boost-your-life.de</p>';
                $errorRegister = '<a href="http://localhost:8888/Boost-Your-Life/resources/login.php?token=' . $xsrfTokenRegisterActivate . '&id=' . $data["id"] . '">Klicken Sie hier um Ihren Account zu aktivieren! (Dieser Link besteht nur, weil man über MAMP keine E-Mails verschicken kann!)</a>';

                // Free Googie Host Server
                // $errorRegister = '<a href="http://boost-your-life.thats.im/resources/login.php?token=' . $xsrfTokenRegisterActivate . '&id=' . $data["id"] . '">Klicken Sie hier um Ihren Account zu aktivieren! (Dieser Link besteht nur, weil man bei dem Kostenlosen Server sehr oft keine E-Mails bekommt und es auch ein E-Mail Limit gibt!)</a>';
            } else {
                // Den Token gibt es nicht in der Datenbank, zeige einen Fehler aus
                $errorRegister = '<p class="errorMessages">Bitte versuchen Sie es nach einem Refresh erneut! Wenn nach dem Refresh das Problem immer noch besteht, so wenden Sie sich bitte an den Support ===> support@boost-your-life.de</p>';
            }
        } else {
            // Wenn es die E-Mail schon in der Datenbank gibt, zeige einen Fehler an und erhöhe den Counter
            $_SESSION['registerCount']++;
            $errorEmail = '<p class="errorMessages">Diese E-Mail Adresse ist schon vergeben, bitte eine andere eingeben!</p>';
        }
    }
}
// Der Header wird eingebunden
include ROOT_DIR . 'includes/assets/header.php';
?>
    <main>
        <div class="formFlexCenter">
            <form class="formRegister" method="post">
                <div>
                    <?php if (isset($errorRegister)) {echo $errorRegister;}?>
                    <h1>Registrieren</h1>
                </div>
                <div class="field">
                    <input type="text" name="firstname" id="firstnameRegister" placeholder="Vorname" title="Beispiel = Manfred Müller" autofocus maxlength="75" value="<?=$firstnameInput;?>">
                    <div><?=$errorFirstName;?></div>
                </div>
                <div class="field">
                    <input type="text" name="lastname" id="lastnameRegister" placeholder="Nachname" title="Beispiel = Trautmann-Neuhagen" maxlength="75" value="<?=$lastnameInput;?>">
                    <div><?=$errorLastName;?></div>
                </div>
                <div class="field">
                    <input type="email" name="email" id="emailRegister" placeholder="E-Mail Adresse" title="Beispiel = max-Mustermann123@gmail.com" maxlength="255" value="<?=$emailInput;?>">
                    <div><?=$errorEmail;?></div>
                </div>
                <div class="field">
                    <input type="password" name="password" id="passwordRegister" placeholder="Passwort" title="Das Passwort muss einen klein Buchstaben, groß Buchstaben, eine Zahl und ein Sonderzeichen enthalten! Mindestens müssen es 10 Zeichen sein!" autocomplete="off" maxlength="100">
                    <div><?=$errorPassword;?></div>
                </div>
                <div class="field">
                    <input type="password" name="passwordRepeat" id="passwordRepeatRegister" placeholder="Passwort Wiederholen" title="Das Passwort muss wiederholt werden!" autocomplete="off" maxlength="100">
                    <div><?=$errorPasswordRepeat;?></div>
                </div>
                <div class="field">
                    <input type="checkbox" name="agbCheckbox" id="agbCheckboxRegister"><a class="agbDatenschutzLink" href="agb.php">AGB</a> & <a class="agbDatenschutzLink" href="datenschutz.php">DATENSCHUTZBESTIMMUNGEN</a> Akzeptieren
                    <div><?=$errorAGBCheckbox;?></div>
                </div>
                <input class="btn btnSecondary" type="submit" value="REGISTRIEREN">
                <input type="hidden" name="xsrfToken" value="<?=$xsrfToken = $tokenClass->generateXSRFToken();
database::getConnections()->tokenInsert($xsrfToken, $xsrfType, $xsrfExpires, $xsrfId = null);?>">
                <br>
                <a href="login.php">Ich besitze einen Account!</a>
            </form>
        </div>
    </main>
    </body>
</html>