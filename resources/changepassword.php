<?php
// Die Config Datei wird required, also reingeladen
require_once '../core/init.php';
// Der Header wird eingebunden
include ROOT_DIR . 'includes/assets/header.php';
// Speichere die Userdaten Klasse in die Variable
$newUser = new user();

// Wenn der Cookie und die Get Variablen nicht existieren, dann mache sie leer
if (!isset($_GET['token'])) {
    $_GET['token'] = "";
}

if (!isset($_GET['id'])) {
    $_GET['id'] = "";
}

// Speichere die $_GET in Variablen
$getForgotPasswordToken = $_GET["token"];
$getForgotPasswordId = $_GET["id"];

// Wenn der User nicht angemeldet ist und die get Variablen nicht da sind, dann lasse ihn icht drauf
if (empty($getForgotPasswordToken) && empty($getForgotPasswordId) && empty($_SESSION["id"])) {
    $newUser->logout();
    redirect::to('404');
}

// Speichere die Token Klasse in die Variable
$tokenClass = new token();
$xsrfType = "ChangePasswordForm";
$xsrfExpires = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +2 minutes"));

$errorPassword = $errorPasswordRepeat = "";

// Wenn der Server Request Method == "Post", dann mach bitte weiter
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Speichere alle eingaben in Variablen und filter diese direkt
    $passwordInput = sanitize_input($_POST['password']);
    $passwordRepeatInput = sanitize_input($_POST['passwordRepeat']);

    // Wenn nichts eingegeben wurde, dann führe den Fehler aus
    if (empty($passwordInput) && empty($passwordRepeatInput)) {
        $errorChangePassword = '<p class="errorMessages">Bitte geben Sie etwas in die Passwort Felder ein!</p>';
    } else {
        // Speichere die Validation Klasse in die Variable
        $validation = new validation();
        // Wenn was eingegeben wurde, dann validiere es
        $changePasswordValidation = $validation->validChangePassword($passwordInput, $passwordRepeatInput);
    }

    // Wenn die Validierung erfolgreich geklappt hat, mache weiter
    if ($changePasswordValidation == true) {
        // Schau ob es den Token in der Datenbank gibt und wenn ja dann mach weiter
        if (($tokenData = $tokenClass->checkXSRFToken($_POST["xsrfToken"])) == true) {
            // Schaut nach ob der Token in der "token" Tabelle existiert

            // Hashe das Passwort und speichere es in einer neuen Variable
            $password_hash = password_hash($passwordInput, PASSWORD_DEFAULT);
            // Speichere die Ip Adresse in einer Variable
            $ipAddress = get_client_ip();

            $tokenForgotPassword = database::getConnections()->view("token", "tokenstring=:tokenstring", [':tokenstring' => $getForgotPasswordToken]);
            $dataUser = database::getConnections()->view("userdata", "id=:id", [':id' => $getForgotPasswordId]);

            // Wenn die $_GET nicht leer ist und noch der Type == "ForgotPasswordActivate" ist, dann mach weiter
            if (!empty($_GET["token"]) && !empty($_GET["id"]) && $tokenForgotPassword["type"] == "ForgotPasswordActivate") {

                // Es wird überprüft ob die Get Variablen mit dem Token in der "token" Tabelle übereinstimmen
                if ($getForgotPasswordToken == $tokenForgotPassword["tokenstring"] && $getForgotPasswordId == $tokenForgotPassword["userid"]) {
                    // Updatet das Passwort des Users
                    if (database::getConnections()->updateChangePassword("userdata", $dataUser["id"], $password_hash) == true) {
                        $errorChangePassword = '<p class="success">Sie haben Erfolgreich Ihr Passwort geändert! Sie haben nochmal eine Sicherheits E-Mail bekommen! <a href="login.php">Hier gehts zum Login!</a></p>';

                        // Schreibe dem User eine E-Mail mit der Info, dass sein Passwort geändert wurde, für seine eigene Sicherheit
                        $newMail = new mail();
                        $newMail->changePasswordMail($dataUser["email"], $ipAddress);
                    } else {
                        $errorChangePassword = '<p class="error">Es ist ein Fehler aufgetreten und Ihr Passwort konnte nicht geändert werden. Bitte wende dich an den Support ===> support@boost-your-life.de</p>';
                    }

                    // Lösche alle Tokens, die den gleichen Type haben und abgelaufen sind
                    $tokenClass->deleteXSRFToken($tokenData["id"], $xsrfType);
                    $tokenClass->deleteXSRFToken($tokenForgotPassword["id"], $tokenForgotPassword["type"]);
                }

                // Wenn die Get Variablen leer sind, dann ist der User angemeldet und will ganz normal sein Password ändern
            } else {
                // Schaue ob es den User in der Datenbank gibt
                $data = database::getConnections()->view("userdata", "id=:id", [':id' => $_SESSION["id"]]);
                // Gibt es den User, dann gehe weiter
                if ($data == true) {

                    // Updatet das Passwort des Users
                    if (database::getConnections()->updateChangePassword("userdata", $data["id"], $password_hash) == true) {
                        $errorChangePassword = '<p class="success">Sie haben Erfolgreich Ihr Passwort geändert! Sie haben nochmal eine Sicherheits E-Mail bekommen! <a href="login.php">Hier gehts zum Login!</a></p>';

                        // Schreibe dem User eine E-Mail mit der Info, dass sein Passwort geändert wurde, für seine eigene Sicherheit
                        $newMail = new mail();
                        $newMail->changePasswordMail($data["email"], $ipAddress);
                    } else {
                        $errorChangePassword = '<p class="error">Es ist ein Fehler aufgetreten und Ihr Passwort konnte nicht geändert werden. Bitte wende dich an den Support ===> support@boost-your-life.de</p>';
                    }
                    // Lösche alle Tokens, die den gleichen Type haben und abgelaufen sind
                    $tokenClass->deleteXSRFToken($tokenData["id"], $xsrfType);

                }
            }

        } else {
            // Den Token gibt es nicht in der Datenbank, zeige einen Fehler aus
            $errorChangePassword = '<p class="errorMessages">Bitte versuchen Sie es nach einem Refresh erneut! Wenn nach dem Refresh das Problem immer noch besteht, so wenden Sie sich bitte an den Support ===> support@boost-your-life.de</p>';
        }
    }
}
?>
    <main>
        <div class="formFlexCenter">
            <form class="formChangePassword" method="post">
                <div>
                    <?php if (isset($errorChangePassword)) {echo $errorChangePassword;}?>
                    <h1>Passwort Ändern</h1>
                </div>
                <div class="field">
                    <input type="password" name="password" id="passwordChange" placeholder="Passwort" title="Das Passwort muss einen klein Buchstaben, groß Buchstaben, eine Zahl und ein Sonderzeichen enthalten! Mindestens müssen es 10 Zeichen sein!" autofocus autocomplete="off" maxlength="100">
                    <div><?=$errorPassword;?></div>
                </div>
                <div class="field">
                    <input type="password" name="passwordRepeat" id="passwordRepeatChange" placeholder="Passwort Wiederholen" title="Das Passwort muss wiederholt werden!" autocomplete="off" maxlength="100">
                    <div><?=$errorPasswordRepeat;?></div>
                </div>
                <div class="field">
                    <input type="checkbox" name="checkboxBot" required><span>Bestätigen Sie, dass Sie Ihr Passwort ändern wollen und kein Bot sind!</span>
                </div>
                <input class="btn btnSecondary" type="submit" value="Password Ändern">
                <input type="hidden" name="xsrfToken" value="<?=$xsrfToken = $tokenClass->generateXSRFToken();
database::getConnections()->tokenInsert($xsrfToken, $xsrfType, $xsrfExpires, $xsrfId = null);?>">
            </form>
        </div>
    </main>
</body>
</html>