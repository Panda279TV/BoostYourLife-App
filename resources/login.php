<?php
// Die Config Datei wird required, also reingeladen
require_once '../core/init.php';
// Speichere die Userdaten Klasse in die Variable
$newUser = new user();

// Wenn der Cookie und die Get Variablen nicht existieren, dann mache sie leer
if (!isset($_COOKIE['RememberMe'])) {
    $_COOKIE['RememberMe'] = "";
}

if (!isset($_GET['token'])) {
    $_GET['token'] = "";
}

if (!isset($_GET['id'])) {
    $_GET['id'] = "";
}

// Speichere die $_GET in Variablen
$getRegisterToken = $_GET["token"];
$getRegisterId = $_GET["id"];

// Schaut nach ob der Token in der "token" Tabelle existiert
$tokenRegisterActivate = database::getConnections()->view("token", "tokenstring=:tokenstring", [':tokenstring' => $getRegisterToken]);

// Wenn die $_GET nicht leer ist und noch der Type == "RegisterAccountActivate" ist, dann mach weiter
if (!empty($_GET["token"]) && !empty($_GET["id"]) && $tokenRegisterActivate["type"] == "RegisterAccountActivate") {
    // Es wird überprüft ob die Get Variablen mit dem Token in der "token" Tabelle übereinstimmen
    if ($getRegisterToken == $tokenRegisterActivate["tokenstring"] && $getRegisterId == $tokenRegisterActivate["userid"]) {
        // Wenn das Updaten nicht geklappt hat, dann zeig eine Fehlermeldung aus
        if (database::getConnections()->updateRegisterAccountActivate("userinfo", $tokenRegisterActivate["userid"], true) == false) {
            $errorLogin = '<p class="error">Beim bestätigen Ihrer E-Mail Adresse und somit das aktivieren Ihres Account ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut mit dem Link in Ihrer E-Mail. Falls es Probleme gibt, wenden Sie sich bitte an den Support = support@boost-your-life.de</p>';
        } else {

            // Wenn Erfolgreich, dann bitte dies ausführen
            $errorLogin = '<p class="success">Ihre E-Mail Adresse wurde erfolgreich bestätigt und somit wurde Ihr Account aktiviert. Wir wünschen Ihnen viel Spaß!</p>';

            //SELECT userid FROM XY WHERE registered
            $deleteDataID = database::getConnections()->viewAllUserID("userinfo", "active=false");

            // Lösche alle User die active == false sind und dazu mehr als 1 Tag alt sind
            if (database::getConnections()->delete("userinfo", "active=:active AND registered< '" . date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " -1440 minutes")) . "'", [":active" => false]) == true) {
                // Mach eine schleife mit den Userids, die gelöscht wurden
                foreach ($deleteDataID as $ids) {
                    // Mache die Ids Einzigartig
                    $idFinish = array_unique($ids);
                    $id = implode(" ", $idFinish);
                    database::getConnections()->delete("userdata", "id=:id", [':id' => $id]);
                }
            }

            // Lösche alle Tokens, die den gleichen Type haben und abgelaufen sind
            database::getConnections()->delete("token", "type=:type AND expires< '" . date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " -1440 minutes")) . "' ", [":type" => $$tokenRegisterActivate["type"]]);
        }
    }
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

// Wenn die $_POSTS und $_SESSION nicht existieren, dann mache sie leer
if (!isset($_POST['xsrfToken'])) {
    $_POST['xsrfToken'] = "";
}

if (!isset($_POST['rememberMe'])) {
    $_POST['rememberMe'] = "";
}

if (!isset($_SESSION['loginCount'])) {
    $_SESSION['loginCount'] = 0;
}

// Ist die $_SESSION['registerCount'] auf 6, dann logge den User aus und schicke ihn auf die Error Seite
if ($_SESSION['loginCount'] > 4) {
    $newUser->logout();
    redirect::to('404');
}

// Speichere die Token Klasse in die Variable
$tokenClass = new token();
$xsrfType = "LoginForm";
$xsrfExpires = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +2 minutes"));

// Wenn der Server Request Method == "Post", dann mach bitte weiter
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Speichere alle eingaben in Variablen und filter diese direkt
    $emailInput = sanitize_input($_POST['email']);
    $passwordInput = sanitize_input($_POST['password']);
    $rememberMe = $_POST['rememberMe'];

    // Schaue ob es die E-Mail schon in der Dtaenbank vorhanden ist, also ob es den User überhaupt gibt
    $data = database::getConnections()->view("userdata", "email=:email", [':email' => $emailInput]);
    $dataUserInfo = database::getConnections()->view("userinfo", "userid=:userid", [':userid' => $data["id"]]);

    if (empty($emailInput) && empty($passwordInput)) {
        $errorLogin = '<p class="errorMessages">Bitte geben Sie eine E-Mail Adresse oder ein Passwort ein!</p>';
    } elseif ($data == false) {
        $_SESSION['loginCount']++;
        $errorLogin = '<p class="errorMessages">Die E-Mail oder das Passwort sind falsch! Versuchen Sie es noch einmal erneut!</p>';
    } elseif ($dataUserInfo["status"] == true) {
        $newUser->logout();
        $errorLogin = '<p class="errorMessages">Ihr Account ist gesperrt! Falls dazu fragen offen sind, wenden Sie sich bitte an den Support ===> support@boost-your-life.de</p>';
    } elseif ($dataUserInfo["active"] == false) {
        $newUser->logout();
        $errorLogin = '<p class="errorMessages">Ihr Account ist noch nicht Aktiviert worden! Bitte schauen Sie in Ihren E-Mails nach, auch in den Spam Ordner! Falls Sie keine E-Mail bekommen haben, wenden Sie sich bitte an den Support ===> support@boost-your-life.de</p>';
    } elseif ($data == true) {
        // Speichere die Validation Klasse in die Variable
        $valid = new validation();
        // Schau ob das eingegebene Passwort mit der E-Mail Adresse in dem Eingabefeld und der Datenbank übereinstimmen
        $validLogin = $valid->validPasswordVerify($data, $passwordInput);
        // Wenn die E-Mail Adresse und das Passwort stimmen, dann geh bitte weiter
        if ($validLogin == true) {
            // Schau ob es den Token in der Datenbank gibt und wenn ja dann mach weiter
            if (($tokenData = $tokenClass->checkXSRFToken($_POST["xsrfToken"])) == true) {
                // Wenn die Checkbox RememberMe angeklickt wurde, dann erstelle einen neuen Token und speichere diesen in dem Cookie und der Datenbank mit der ID des Users
                if (!empty($rememberMe)) {
                    $xsrfTypeRememberMe = "RememberMeToken";
                    // 7 Tage = 10080 minutes // 14 Tage = 20160 minutes  // 31 Tage = 44640 minutes
                    $xsrfExpiresRememberMe = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +20160 minutes"));
                    $xsrfTokenRememberMe = $tokenClass->generateXSRFToken();
                    database::getConnections()->tokenInsert($xsrfTokenRememberMe, $xsrfTypeRememberMe, $xsrfExpiresRememberMe, $data["id"]);
                    setcookie("RememberMe", $xsrfTokenRememberMe, time() + 3600 * 24 * 31);
                } elseif (empty($_COOKIE['RememberMe'])) {
                    // Wenn die Checkbox nicht angeklickt wird, dann lösche bitte alle Tokens
                    database::getConnections()->delete("token", "userid=:userid AND expires<NOW()", [":userid" => $data["id"]]);
                }
                // Speichere die User Daten in die Sessions
                session::put("id", $data["id"]);
                session::put("firstname", $data["firstname"]);
                session::put("lastname", $data["lastname"]);
                session::put("email", $data["email"]);
                session::put("rights", $dataUserInfo["rights"]);
                // Speichere das Datum und die Ip Adresse in Variablen
                $lastLogin = date("Y-m-d H:i:s");
                $ipAddress = get_client_ip();

                // Wenn der Login erfolgreich war, Update den lastlogin
                database::getConnections()->updateLoginUserInfo("userinfo", $data["id"], $lastLogin, $ipAddress);

                // Lösche alle Tokens, die den gleichen Type haben und abgelaufen sind
                $tokenClass->deleteXSRFToken($tokenData["id"], $xsrfType);
                // Wenn der Login erfolgreich war, dann mach den Count der $_SESSION['loginCount'] auf 0
                $_SESSION['loginCount'] = 0;

                // Schreibe dem User eine E-Mail mit allen Login Daten, für seine eigene Sicherheit
                $newMail = new mail();
                $newMail->loginDataMail($data["email"], $data["firstname"], $data["lastname"], $ipAddress);

                // 1 Sekunde abwarten und dann weiter machen
                sleep(1);

                // Logge ihn ein und leite ihn weiter zur nächsten Seite
                redirect::to('resources/home');
            } else {
                // Den Token gibt es nicht in der Datenbank, zeige einen Fehler aus
                $errorLogin = '<p class="errorMessages">Bitte versuchen Sie es nach einem Refresh erneut! Wenn nach dem Refresh das Problem immer noch besteht, so wenden Sie sich bitte an den Support ===> support@web.de</p>';
            }
        }
    }
}
// Der Header wird eingebunden
include ROOT_DIR . 'includes/assets/header.php';
?>
    <main>
        <div class="formFlexCenter">
            <form class="formLogin" method="post">
                <div>
                    <?php if (isset($errorLogin)) {echo $errorLogin;}?>
                    <h1>Login</h1>
                </div>
                <div class="field">
                    <input type="email" name="email" id="emailLogin" autocomplete="off" placeholder="E-Mail Adresse" title="Deine E-Mail Adresse, mit der du dich registriert hast!" autofocus maxlength="255">
                </div>
                <div class="field">
                    <input type="password" name="password" id="passwordLogin" autocomplete="off" placeholder="Passwort" title="Dein Passwort, mit dem du dich registriert hast!" maxlength="100">
                </div>
                <div class="field">
                    <input type="checkbox" name="rememberMe" id="rememberMe">Eingeloggt bleiben
                    <br>
                    <a href="forgotpassword.php">Passwort vergessen?</a>
                </div>
                <input class="btn btnSecondary" type="submit" value="EINLOGGEN">
                <input type="hidden" name="xsrfToken" value="<?=$xsrfToken = $tokenClass->generateXSRFToken();
database::getConnections()->tokenInsert($xsrfToken, $xsrfType, $xsrfExpires, $xsrfId = null);?>">
                <br>
                <a href="register.php">Noch kein Account?</a>
            </form>
        </div>
    </main>
    </body>
</html>