<?php
// Die Config Datei wird required, also reingeladen
require_once '../core/init.php';
// Der Header wird eingebunden
include ROOT_DIR . 'includes/assets/header.php';
// Speichere die Userdaten Klasse in die Variable
$newUser = new user();

// Wenn die $_SESSION nicht existieren, dann mache sie leer
if (!isset($_SESSION['forgotPasswordCount'])) {
    $_SESSION['forgotPasswordCount'] = 0;
}

// Ist die $_SESSION['forgotPasswordCount'] auf 3, dann logge den User aus und schicke ihn auf die Error Seite
if ($_SESSION['forgotPasswordCount'] > 2) {
    $newUser->logout();
    redirect::to('404');
}

// Speichere alle Variablen als leer
$errorForgotPassword = $errorFirstName = $errorLastName = $errorEmail = $firstnameInput = $lastnameInput = "";

// Speichere die Token Klasse in die Variable
$tokenClass = new token();
$xsrfType = "ForgotPasswordForm";
$xsrfExpires = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +5 minutes"));

// Wenn der Server Request Method == "Post", dann mach bitte weiter
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Speichere alle eingaben in Variablen und filter diese direkt
    $firstnameInput = sanitize_input(ucfirst($_POST['firstname']));
    $lastnameInput = sanitize_input(ucfirst($_POST['lastname']));
    $emailInput = sanitize_input($_POST['email']);

    // Wenn nichts eingegeben wurde, dann führe den Fehler aus
    if (empty($firstnameInput) && empty($lastnameInput) && empty($emailInput)) {
        $errorForgotPassword = '<p class="errorMessages">Bitte geben Sie etwas in die Felder ein!</p>';
    } else {
        // Speichere die Validation Klasse in die Variable
        $validation = new validation();
        // Wenn was eingegeben wurde, dann validiere es
        $forgotPasswordValidation = $validation->validForgotPassword($firstnameInput, $lastnameInput, $emailInput);
    }

    // Wenn die Validierung erfolgreich geklappt hat, mache weiter
    if ($forgotPasswordValidation == true) {
        // Schaue ob es die E-Mail schon in der Dtaenbank vorhanden ist, also ob es den User überhaupt gibt
        $data = database::getConnections()->view("userdata", "email=:email", [':email' => $emailInput]);
        // Wenn es den User/E-Mail nicht gibt, dann gib bitte einen Fehler aus
        if ($data == false) {
            $_SESSION['forgotPasswordCount']++;
            $errorEmail = '<p class="errorMessages">Diese E-Mail Adresse gibt es bei uns nicht, bitte eine andere eingeben!</p>';
            // Stimmt die Eingabe Vorname, mit der in der Datenbank nicht überein, dann zeige ein Fehler an
        } elseif ($firstnameInput != $data["firstname"]) {
            $errorFirstName = '<p class="errorMessages">Bitte geben Sie zu der passenden E-Mail auch den passenden Vornamen an, den Sie bei uns angegeben haben!</p>';
            // Stimmt die Eingabe Nachname, mit der in der Datenbank nicht überein, dann zeige ein Fehler an
        } elseif ($lastnameInput != $data["lastname"]) {
            $errorLastName = '<p class="errorMessages">Bitte geben Sie zu der passenden E-Mail auch den passenden Nachnamen an, den Sie bei uns angegeben haben!</p>';
            // Ist alles erfolgreich, dann mach weiter
        } elseif ($data == true) {
            // Schau ob es den Token in der Datenbank gibt und wenn ja dann mach weiter
            if (($tokenData = $tokenClass->checkXSRFToken($_POST["xsrfToken"])) == true) {

                // Speichere die Ip Adresse in einer Variable
                $ipAddress = get_client_ip();

                // Erfolgreich den ersten Schritt von dem Passwort Vergessen geschafft
                echo '<p class="success">Der erste Schritt vom Passwort Vergessen ist geschafft. Wir haben dir eine E-Mail mit einem Link zugeschickt, dieser ist nur 1 Stunde gültig! Schau bitte auch im Spam Ordner. Falls du keine E-Mail bekommen hast, wende dich bitte an den Support ===> support@boost-your-life.de</p>';

                // Es wird ein Token erstellt zum Vergessen des Passworts, dieser wird auch hochgeladen in die "token" Tabelle
                $xsrfTypeForgotPasswordActivate = "ForgotPasswordActivate";
                $xsrfExpiresForgotPasswordActivate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +60 minutes"));
                $xsrfTokenForgotPasswordActivate = $tokenClass->generateXSRFToken();
                database::getConnections()->tokenInsert($xsrfTokenForgotPasswordActivate, $xsrfTypeForgotPasswordActivate, $xsrfExpiresForgotPasswordActivate, $data["id"]);

                // Lösche alle Tokens, die den gleichen Type haben und abgelaufen sind
                $tokenClass->deleteXSRFToken($tokenData["id"], $xsrfType);
                $tokenClass->deleteXSRFToken($tokenData["id"], $xsrfTypeForgotPasswordActivate);

                // Schreibe dem User eine E-Mail mit dem Passwort Vergessen Link
                $newMail = new mail();
                $newMail->forgotPasswordMail($emailInput, $data["id"], $ipAddress, $xsrfTokenForgotPasswordActivate);

                // ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //
                // Diese zwei Links hier unten drunter sind nur dafür da, für die Entwicklung, also wenn man keine E-Mail enthalten kann
                $errorForgotPassword .= '<a href="http://localhost:8888/Boost-Your-Life/resources/changepassword.php?token=' . $xsrfTokenForgotPasswordActivate . '&id=' . $data["id"] . '">Klicken Sie hier um Ihr Passwort zu vergessen! (Dieser Link besteht nur, weil man über MAMP keine E-Mails verschicken kann!)</a>';

                // Free Googie Host Server
                // $errorForgotPassword = '<a href="http://boost-your-life.thats.im/resources/changepassword.php?token=' . $xsrfTokenForgotPasswordActivate . '&id=' . $data["id"] . '">Klicken Sie hier um Ihr Passwort zu vergessen! (Dieser Link besteht nur, weil man bei dem Kostenlosen Server sehr oft keine E-Mails bekommt und es auch ein E-Mail Limit gibt!)</a>';
            } else {
                // Den Token gibt es nicht in der Datenbank, zeige einen Fehler aus
                $errorForgotPassword = '<p class="errorMessages">Bitte versuchen Sie es nach einem Refresh erneut! Wenn nach dem Refresh das Problem immer noch besteht, so wenden Sie sich bitte an den Support ===> support@boost-your-life.de</p>';
            }
        }
    }
}
?>
    <main>
        <div class="formFlexCenter">
            <form class="formForgotPassword" method="post">
                <div>
                    <?php if (isset($errorForgotPassword)) {echo $errorForgotPassword;}?>
                    <h1>Passwort Vergessen</h1>
                </div>
                <div class="field">
                    <!-- <label for="firstnameForgotPassword">Vorname:</label> -->
                    <input type="text" name="firstname" id="firstnameForgotPassword" placeholder="Vorname" title="Deinen Vornamen!" autofocus maxlength="75" value="">
                    <div><?=$errorFirstName;?></div>
                </div>
                <div class="field">
                    <!-- <label for="lastnameForgotPassword">Nachname:</label> -->
                    <input type="text" name="lastname" id="lastnameForgotPassword" placeholder="Nachname" title="Deinen Nachname!" maxlength="75" value="">
                    <div><?=$errorLastName;?></div>
                </div>
                <div class="field">
                    <!-- <label for="emailForgotPassword">Email Adresse:</label> -->
                    <input type="email" name="email" id="emailForgotPassword" placeholder="E-Mail Adresse" title="Deine E-Mail Adresse!" maxlength="255" value="">
                    <div><?=$errorEmail;?></div>
                </div>
                <div class="field">
                    <input type="checkbox" name="checkboxBot" required><span>Bestätigen Sie, dass Sie Ihr Passwort Vergessen wollen und kein Bot sind!</span>
                </div>
                <input class="btn btnSecondary" type="submit" name="edit" value="Passwort Vergessen">
                <input type="hidden" name="xsrfToken" value="<?=$xsrfToken = $tokenClass->generateXSRFToken();
database::getConnections()->tokenInsert($xsrfToken, $xsrfType, $xsrfExpires, $xsrfId = null);?>">
            </form>
        </div>
    </main>
</body>
</html>