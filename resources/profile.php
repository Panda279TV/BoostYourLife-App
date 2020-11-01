<?php
// Die Config Datei wird required, also reingeladen
require_once '../core/init.php';
// Der Header wird eingebunden
include ROOT_DIR . 'includes/assets/header.php';

// Speichere die Userdaten Klasse in die Variable
$newUser = new user();
$userid = $newUser->userId;

// Schaut ob der User eingeloggt ist oder nicht, da dass ein Bereich nach dem Login ist, ist es wichtig ausgeloggte User nicht weiter kommen zu lassen
if ($newUser->is_loggedIn() == false) {
    $newUser->logout();
    redirect::to('404');
}
$result = database::getConnections()->view("userdata", "id=:id", [':id' => $userid]);
// Schaut ob ein Profil Bild in der Datenbank vorhanden ist
$profilPic = database::getConnections()->view("images", "userid=:userid AND tablename=:tablename ORDER BY uploaded DESC LIMIT 1", [':userid' => $userid, ':tablename' => 'Profile']);
// Wenn das Bild leer ist, dann verwende das standard Default Bild
if (empty($profilPic["src"])) {
    $profilPic["src"] = "public/image/DefaultProfilePic.png";
}
// Schaut ob das Datum richtig ist, da DATE in der Datenbank immer 01.01.1970 anzeigt bekommt man diese anzeige so weg auf der Seite
$birhtdayDate = "";
$bdayDate = strtotime($result['birthday']);
if ($bdayDate) {
    $birhtdayDate = date('d-m-Y', $bdayDate);
}
?>
<main>
    <br>
    <div class="profileDataWrapper">
        <img src="<?=ROOT_URL . $profilPic["src"]?>" alt="<?=$profilPic["name"]?> " oncontextmenu="return false;">
        <h1><?=$_SESSION['firstname'] . " " . $_SESSION['lastname'];?></h1>
        <p><?=$birhtdayDate?></p>
        <p><?=$result['country']?></p>
        <form action="" method="post" enctype="multipart/form-data">
            <input class="btn btnPrimary" type="submit" name="edit" value="Profil Editieren">
        </form>
        <br>
    </div>
<?php
// Wenn der $_POST nicht existieren, dann mache ihn leer
if (!isset($_POST['edit'])) {
    $_POST['edit'] = "";
}

// Es wird beim Post geschaut was drinne steht und dann wird die jeweilige Funktion aufgerufen
if ($_POST["edit"] == 'Profil Editieren') {
    profileShow();
} elseif ($_POST['edit'] == 'Aktualisieren') {
    profileUpdate();
}

// Die Funktion zeige das Formular um die Daten zu bearbeiten
function profileShow()
{
    // Speichere alle Variablen als leer
    $errorUpdate = $errorFirstName = $errorLastName = $errorEmail = $errorPassword = $errorPasswordRepeat = $xsrfToken = $errorImage = "";
    // Hole dir die Global Variable $newUser um davon die ID zu bekommen
    global $newUser, $userid, $profilPic, $errorImage;
    // Hole die User Daten aus der Datenbank, sodass der User sieht, was er eingespeichert hat von seinen Sachen
    $result = database::getConnections()->view("userdata", "id=:id", [':id' => $userid]);

    // Speichere die Token Klasse in die Variable
    $tokenClass = new token();
    $xsrfType = "ProfileForm";
    $xsrfExpires = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +5 minutes"));
    // Generiere ein Token und speichere diesen in der "token" Tabelle
    $xsrfToken = $tokenClass->generateXSRFToken();
    database::getConnections()->tokenInsert($xsrfToken, $xsrfType, $xsrfExpires, $xsrfId = null);

    // Gebe das Formular aus um es zu editieren
    echo '<br><form class="textAlignCenter" method="post" enctype="multipart/form-data">
    <input type="hidden" name="passwordHidden" value="' . $result['password'] . '">
    <input type="hidden" name="valueProfileImage" value="' . $profilPic['src'] . '">
    <input type="hidden" name="valueBirthday" value="' . $result['birthday'] . '">
    <input type="hidden" name="valueCountry" value="' . $result['country'] . '">
    <div>' . $errorUpdate . '</div>

    <div class="field">
        <label for="profileImage">Profilbild:</label>
        <input type="file" accept="image/*" name="appImage" id="profileImage">
        <div>' . $errorImage . '</div>
    </div>
    <div class="field">
        <label for="firstname">Vorname:</label>
        <input type="text" name="firstname" id="firstname" placeholder="Max" autofocus maxlength="55" value="' . $result['firstname'] . '">
        <div>' . $errorFirstName . '</div>
    </div>
    <div class="field">
        <label for="lastname">Nachname:</label>
        <input type="text" name="lastname" id="lastname" placeholder="Mustermann" autofocus maxlength="55" value="' . $result['lastname'] . '">
        <div>' . $errorLastName . '</div>
    </div>
    <div class="field">
        <label for="email">E-Mail Adresse:</label>
        <input type="email" name="email" id="email" placeholder="max-Mustermann@gmail.com" maxlength="255" value="' . $result['email'] . '">
        <div>' . $errorEmail . '</div>
    </div>
    <div class="field">
        <label for="bDay">Geburtstag:</label>
        <input type="date" name="birthday" id="birthday" value="' . date("Y-m-d", strtotime($result['birthday'])) . '">
    </div>
    <div class="field">
        <label for="country">Land:</label>
        <select class="" name="country">
            <optgroug name="country">
                <option value="" disabled selected>' . $result['country'] . '</option>
                <option value="Albanien">Albanien</option>
                <option value="Belgien">Belgien</option>
                <option value="Bosnien und Herzegowina">Bosnien und Herzegowina</option>
                <option value="Bulgarien">Bulgarien</option>
                <option value="Dänemark">Dänemark</option>
                <option value="Deutschland">Deutschland</option>
                <option value="Estland">Estland</option>
                <option value="Finnland">Finnland</option>
                <option value="Frankreich">Frankreich</option>
                <option value="Griechenland">Griechenland</option>
                <option value="Irland">Irland</option>
                <option value="Island">Island</option>
                <option value="Italien">Italien</option>
                <option value="Kasachstan">Kasachstan</option>
                <option value="Kosovo">Kosovo</option>
                <option value="Kroatien">Kroatien</option>
                <option value="Lettland">Lettland</option>
                <option value="Liechtenstein">Liechtenstein</option>
                <option value="Litauen">Litauen</option>
                <option value="Luxemburg">Luxemburg</option>
                <option value="Malta">Malta</option>
                <option value="Republik Moldau">Republik Moldau</option>
                <option value="Monaco">Monaco</option>
                <option value="Montenegro">Montenegro</option>
                <option value="Niederlande">Niederlande</option>
                <option value="Nordmazedonien">Nordmazedonien</option>
                <option value="Norwegen">Norwegen</option>
                <option value="Österreich">Österreich</option>
                <option value="Polen">Polen</option>
                <option value="Portugal">Portugal</option>
                <option value="Rumänien">Rumänien</option>
                <option value="Russland">Russland</option>
                <option value="San Marino">San Marino</option>
                <option value="Schweden">Schweden</option>
                <option value="Schweiz">Schweiz</option>
                <option value="Serbien">Serbien</option>
                <option value="Slowakei">Slowakei</option>
                <option value="Slowenien">Slowenien</option>
                <option value="Spanien">Spanien</option>
                <option value="Tschechien">Tschechien</option>
                <option value="Türkei">Türkei</option>
                <option value="Ukraine">Ukraine</option>
                <option value="Ungarn">Ungarn</option>
                <option value="Vatikanstadt">Vatikanstadt</option>
                <option value="Vereinigtes Königreich Großbritannien">Vereinigtes Königreich Großbritannien</option>
                <option value="Weißrussland">Weißrussland</option>
            </optgroup>
        </select>
    </div>
    <div class="field">
        <input type="checkbox" name="checkbox" required><span>Bestätigen Sie, dass Sie Ihre Daten ändern wollen und kein Bot sind!</span>
    </div>
    <input class="btn btnSecondary" type="submit" name="edit" value="Aktualisieren">
    <input type="hidden" name="xsrfToken" value="' . $xsrfToken . '">
    <br>
    <a href="changepassword.php">Passwort Ändern</a>
    </form>';
}
// Funktion für das Updaten der Daten des Users
function profileUpdate()
{
    // Speichere alle Variablen als leer
    $errorUpdate = $errorFirstName = $errorLastName = $errorEmail = $errorPassword = $errorPasswordRepeat = $errorImage = "";
    // Hole dir die ganzen Globals Variablen, sodass auch die Fehlermeldungen klappen
    global $newUser, $userid, $errorUpdate, $errorFirstName, $errorLastName, $errorEmail, $errorPassword, $errorPasswordRepeat, $errorImage;

    // Speichere die Token Klasse in die Variable
    $tokenClass = new token();
    $xsrfType = "ProfileForm";

    // Speichere alle eingaben in Variablen und filter diese direkt
    $firstnameInput = sanitize_input(ucfirst($_POST['firstname']));
    $lastnameInput = sanitize_input(ucfirst($_POST['lastname']));
    $emailInput = sanitize_input($_POST['email']);

    // Speichere die Validation Klasse in die Variable
    $validation = new validation();
    $profileValidation = $validation->validProfile($firstnameInput, $lastnameInput, $emailInput);

    // Wenn die Validierung erfolgreich war, dann gehe weiter
    if ($profileValidation == true) {
        // Schaut ob es die eingegebene E-Mail schon gibt
        $data = database::getConnections()->view("userdata", "email=:email", [':email' => $emailInput]);
        // Wenn es die E-Mail nicht in der Datenbank gibt oder diese gleich mit der eigenen E-Mail, dann geh weiter
        if ($data == false || $data["email"] == $emailInput) {
            // Schau ob es den Token in der Datenbank gibt und wenn ja dann mach weiter
            if (($tokenData = $tokenClass->checkXSRFToken($_POST["xsrfToken"])) == true) {

                // Es wird geschaut ob ein Bild hochgeladen wurde
                if (!$_FILES['appImage']['size'] == 0) {
                    // Schaut nach ob bereits ein Bild mit der Userid in der Datenbank vorhanden ist
                    $profilPics = database::getConnections()->view("images", "userid=:userid AND tablename=:tablename", [':userid' => $data["id"], ':tablename' => 'Profile']);

                    // DIe Funktion imageUpload wird reingeladen
                    require_once '../functions/imageUpload.php';

                    // Wenn es ein Bild von dem User gibt, dann lösche den alten Datensatzt und erstelle einen neuen
                    if ($profilPics == true) {
                        $unlink = "..//" . $_POST['valueProfileImage'];

                        $bildFunktion = imageUploads("update", "Profile", $userid, $profilPics["nlpid"], $unlink);

                        // Wurde das Bild erfolgreich hochgeladen, gebe echo aus
                        if ($bildFunktion == false) {
                            global $errorUpdate;
                            $errorUpdate = '<p class="success">Erfolgreich das Profile Bild aktualisiert!</p>';
                        } else {
                            global $errorUpdate;
                            $errorUpdate = '<p class="error">Beim aktualisieren des Bildes ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut!</p>';
                        }

                        // Wenn noch kein Profil Bild des User vorhanden ist, dann lade das neue hoch
                    } else {
                        $unlink = "";
                        $bildFunktion = imageUploads("insert", "Profile", $userid, $profilPics["nlpid"], $unlink);
                    }
                }

                // Schaut ob der Geburtstag leer ist, wenn ja dann nimm bitte die Sachen aus der Hidden Post mit den alten Daten
                if (empty($_POST["birthday"])) {
                    $birthdayInput = $_POST["valueBirthday"];
                } else {
                    $birthdayInput = $_POST["birthday"];
                }
                // Schaut ob das Land leer ist, wenn ja dann nimm bitte die Sachen aus der Hidden Post mit den alten Daten
                if (empty($_POST["country"])) {
                    $countryInput = $_POST["valueCountry"];
                } else {
                    $countryInput = $_POST["country"];
                }

                // Wenn alles erfolgreich war, dann update den User
                if ($updateData = database::getConnections()->updateProfileUserData("userdata", $userid, $firstnameInput, $lastnameInput, $emailInput, $birthdayInput, $countryInput) == true) {
                    echo '<p class="success">Ihre Daten wurden erfolgreich aktualisiert!</p>';
                } else {
                    echo '<p class="error">Beim Aktualisieren Ihrer Daten ist ein Fehler aufgetreten!</p>';
                }

                // Lösche alle Tokens, die den gleichen Type haben und abgelaufen sind
                $tokenClass->deleteXSRFToken($tokenData["id"], $xsrfType);

                // Schreibe dem User eine E-Mail mit allen Profil Daten, für seine eigene Sicherheit
                $newMail = new mail();
                $newMail->updateProfileMail($emailInput, $firstnameInput, $lastnameInput, $birthdayInput, $countryInput);

                // Speichere die neuen User Daten in die alten Sessions
                session::put("firstname", $firstnameInput);
                session::put("lastname", $lastnameInput);
                session::put("email", $emailInput);
            } else {
                profileShow();
                // Den Token gibt es nicht in der Datenbank, zeige einen Fehler aus
                global $errorUpdate;
                $errorUpdate = '<p class="errorMessages">Bitte versuchen Sie es nach einem Refresh erneut! Wenn nach dem Refresh das Problem immer noch besteht, so wenden Sie sich bitte an den Support = support@boost-your-life.de</p>';
            }
        } else {
            profileShow();
            // Die eingegebene E-Mail Adresse wird bereits von einem anderen User benutzt, bitte Fehler anzeigen
            global $errorEmail;
            $errorEmail = '<p class="errorMessages">Diese E-Mail Adresse ist schon vergeben, bitte eine andere eingeben!</p>';
        }
        // Wenn Fehler passieren, dann zeig das Formular an, da es beim erfolgreichen Updaten weggeht
    } elseif ($profileValidation == false || $data == true || $tokenData == false || $bildFunktion == true) {
        global $errorUpdate, $errorFirstName, $errorLastName, $errorEmail, $errorPassword, $errorPasswordRepeat, $errorImage;
        $result = database::getConnections()->view("userdata", "id=:id", [':id' => $userid]);
        // Gib das Formular aus
        echo '<br><form class="textAlignCenter" method="post" enctype="multipart/form-data">
            <input type="hidden" name="passwordHidden" autocomplete="off" value="' . $result['password'] . '">
            <input type="hidden" name="valueProfileImage" autocomplete="off" value="' . $profilPic['src'] . '">
            <div>' . $errorUpdate . '</div>

            <div class="field">
                <label for="profileImage">Profilbild:</label>
                <input type="file" accept="image/*" name="appImage" id="profileImage">
                <div>' . $errorImage . '</div>
            </div>
            <div class="field">
                <label for="firstname">Vorname:</label>
                <input type="text" name="firstname" id="firstname" placeholder="Max" autofocus maxlength="55" value="' . $result['firstname'] . '">
                <div>' . $errorFirstName . '</div>
            </div>
            <div class="field">
                <label for="lastname">Nachname:</label>
                <input type="text" name="lastname" id="lastname" placeholder="Mustermann" autofocus maxlength="55" value="' . $result['lastname'] . '">
                <div>' . $errorLastName . '</div>
            </div>
            <div class="field">
                <label for="email">E-Mail Adresse:</label>
                <input type="email" name="email" id="email" placeholder="max-Mustermann@gmail.com" maxlength="255" value="' . $result['email'] . '">
                <div>' . $errorEmail . '</div>
            </div>
            <div class="field">
                <label for="bDay">Geburtstag:</label>
                <input type="date" name="bDay" id="bDay" value="' . date("Y-m-d", strtotime($result['birthday'])) . '">
            </div>
            <div class="field">
                <label for="country">Land:</label>
                <select class="" name="country">
                    <optgroug name="country">
                        <option value="" disabled selected>' . $result['country'] . '</option>
                        <option value="Albanien">Albanien</option>
                        <option value="Belgien">Belgien</option>
                        <option value="Bosnien und Herzegowina">Bosnien und Herzegowina</option>
                        <option value="Bulgarien">Bulgarien</option>
                        <option value="Dänemark">Dänemark</option>
                        <option value="Deutschland">Deutschland</option>
                        <option value="Estland">Estland</option>
                        <option value="Finnland">Finnland</option>
                        <option value="Frankreich">Frankreich</option>
                        <option value="Griechenland">Griechenland</option>
                        <option value="Irland">Irland</option>
                        <option value="Island">Island</option>
                        <option value="Italien">Italien</option>
                        <option value="Kasachstan">Kasachstan</option>
                        <option value="Kosovo">Kosovo</option>
                        <option value="Kroatien">Kroatien</option>
                        <option value="Lettland">Lettland</option>
                        <option value="Liechtenstein">Liechtenstein</option>
                        <option value="Litauen">Litauen</option>
                        <option value="Luxemburg">Luxemburg</option>
                        <option value="Malta">Malta</option>
                        <option value="Republik Moldau">Republik Moldau</option>
                        <option value="Monaco">Monaco</option>
                        <option value="Montenegro">Montenegro</option>
                        <option value="Niederlande">Niederlande</option>
                        <option value="Nordmazedonien">Nordmazedonien</option>
                        <option value="Norwegen">Norwegen</option>
                        <option value="Österreich">Österreich</option>
                        <option value="Polen">Polen</option>
                        <option value="Portugal">Portugal</option>
                        <option value="Rumänien">Rumänien</option>
                        <option value="Russland">Russland</option>
                        <option value="San Marino">San Marino</option>
                        <option value="Schweden">Schweden</option>
                        <option value="Schweiz">Schweiz</option>
                        <option value="Serbien">Serbien</option>
                        <option value="Slowakei">Slowakei</option>
                        <option value="Slowenien">Slowenien</option>
                        <option value="Spanien">Spanien</option>
                        <option value="Tschechien">Tschechien</option>
                        <option value="Türkei">Türkei</option>
                        <option value="Ukraine">Ukraine</option>
                        <option value="Ungarn">Ungarn</option>
                        <option value="Vatikanstadt">Vatikanstadt</option>
                        <option value="Vereinigtes Königreich Großbritannien">Vereinigtes Königreich Großbritannien</option>
                        <option value="Weißrussland">Weißrussland</option>
                    </optgroup>
                </select>
            </div>
            <div class="field">
                <input type="checkbox" name="checkbox" required><span>Bestätigen Sie, dass Sie Ihre Daten ändern wollen und kein Bot sind!</span>
            </div>
            <input class="btn btnSecondary" type="submit" name="edit" value="Aktualisieren">
            <input type="hidden" name="xsrfToken" value="' . $_POST["xsrfToken"] . '">
            <br>
            <a href="changepassword.php">Passwort Ändern</a>
            </form>';
    }
}
?>
</main>
<?php
// Der Footer wird eingebunden
include ROOT_DIR . 'includes/assets/footer.php';
?>