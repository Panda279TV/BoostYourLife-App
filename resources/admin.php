<?php
// Die Config Datei wird required, also reingeladen
require_once '../core/init.php';
// Der Header wird eingebunden
include ROOT_DIR . 'includes/assets/header.php';
// Speichere die Userdaten Klasse in die Variable
$newUser = new user();
$userid = $newUser->userId;

// Speichere die Rechte des Users in einer Variable
$dataRights = database::getConnections()->view("userinfo", "userid=:userid", [':userid' => $userid]);

// Hat der User nicht die benötigten Rechte oder ist nicht angemeldet, dann logge ihn aus und zeig die error Seite an
if ($dataRights["rights"] < 21) {
    $newUser->logout();
    redirect::to('404');
} elseif ($newUser->is_loggedIn() == false) {
    $newUser->logout();
    redirect::to('404');
}
?>
    <main>
<?php

// Schaut welche Rechte der User hat und gibt dann genau dass aus
if ($dataRights["rights"] >= 90) {
    // Gib die Buttons aus
    echo '<br><div class="formAdminMenu"><form method="post" enctype="multipart/form-data">
        <input class="btn btnPrimary" type="submit" name="submit" value="Admin Nlp">
        <input class="btn btnPrimary" type="submit" name="submit" value="Nlp Anlegen">
        <input class="btn btnPrimary" type="submit" name="submit" value="Admin User">
        <input class="btn btnPrimary" type="submit" name="submit" value="User Anlegen">
        <input class="btn btnPrimary" type="submit" name="submit" value="Images">
        <input class="btn btnPrimary" type="submit" name="submit" value="Statistiken">
    </form></div><br>';

    // Wenn die $_POSTS nicht existieren, dann mache sie leer
    if (!isset($_POST['submit'])) {
        $_POST['submit'] = "";
    }

    if (!isset($_POST['xsrfToken'])) {
        $_POST['xsrfToken'] = "";
    }

    if (!isset($_POST['edit'])) {
        $_POST['edit'] = "";
    }

    if (!isset($_POST['confirmDelete'])) {
        $_POST['confirmDelete'] = "";
    }

    if (!isset($_POST['confirmRandomPassword'])) {
        $_POST['confirmRandomPassword'] = "";
    }

    // Viele If und Elseif abfragen. Es wird geschaut, welchen Button der User geklickt hat und dann soll genau diese Funktion aufgerufen werden
    if ($_POST['submit'] == 'Admin User') {
        searchAdmin($dataRights["rights"]);
    } elseif ($_POST['submit'] == 'Admin Nlp') {
        searchNLP();
    } elseif ($_POST['submit'] == 'User Suchen') {
        searchAdmin($dataRights["rights"]);
        tableShowUsers();
    } elseif ($_POST['submit'] == 'NLP Suchen') {
        searchNLP();
        tableShowNlp();
    } elseif ($_POST['edit'] == 'Edit') {
        if ($_POST["tableName"] == "userdata") {
            searchAdmin($dataRights["rights"]);
        } else {
            searchNLP();
        }
        editTableRow($_POST["tableName"]);
    } elseif ($_POST['edit'] == 'Aktualisieren') {
        global $errorSearch;
        if ($_POST["tableName"] == "userdata") {
            searchAdmin($dataRights["rights"]);
        } else {
            searchNLP();
        }
        updateTableRow($_POST["tableName"]);
        echo $errorSearch;
    } elseif ($_POST['edit'] == 'Show') {
        global $errorSearch;
        $errorSearch = '<p class="success">Erfolgreich!</p>';
        if ($_POST["tableName"] == "userdata") {
            searchAdmin($dataRights["rights"]);
            showTableRow("userdata");
        } else {
            searchNLP();
            showTableRow("nlp");
        }
    } elseif ($_POST['edit'] == 'Delete') {
        if ($_POST["tableName"] == "userdata") {
            searchAdmin($dataRights["rights"]);
        } else {
            searchNLP();
        }
        deleteTableRow();
    } elseif ($_POST['confirmDelete'] == 'Yes') {
        global $errorSearch;
        if ($_POST["tableName"] == "userdata") {
            if (database::getConnections()->deleteByID($_POST["tableName"], $_POST["userId"]) == true && database::getConnections()->deleteByUSERID("userinfo", $_POST["userId"]) == true) {
                $errorSearch = '<p class="success">Die Reihe wurde erfolgreich gelöscht!</p>';
            } else {
                $errorSearch = '<p class="error">Es ist ein fehler aufgetreten und die Reihe konnte nicht gelöscht werden!</p>';
            }
            searchAdmin($dataRights["rights"]);
        } else {
            if (database::getConnections()->deleteByID($_POST["tableName"], $_POST["userId"]) == true) {
                $errorSearch = '<p class="success">Die Reihe wurde erfolgreich gelöscht!</p>';
            } else {
                $errorSearch = '<p class="error">Es ist ein fehler aufgetreten und die Reihe konnte nicht gelöscht werden!</p>';
            }
            searchNLP();
        }
    } elseif ($_POST['confirmDelete'] == 'No') {
        global $errorSearch;
        $errorSearch = '<p class="error">Die Reihe wurde nicht gelöscht!</p>';
        if ($_POST["tableName"] == "userdata") {
            searchAdmin($dataRights["rights"]);
        } else {
            searchNLP();
        }
    } elseif ($_POST['edit'] == 'Ent/Sperren') {
        global $errorSearch;
        if (empty($_POST["status"])) {
            $status = 1;
            if (database::getConnections()->updateStatusAdmin("userinfo", $_POST["userId"], $status) == true) {
                $errorSearch = '<p class="success">Erfolgreich gesperrt!</p>';
            } else {
                $errorSearch = '<p class="error">Beim Sperren ist ein Fehler aufgetreten!</p>';
            }
        } else {
            $status = 0;
            if (database::getConnections()->updateStatusAdmin("userinfo", $_POST["userId"], $status) == true) {
                $errorSearch = '<p class="success">Erfolgreich entsperrt!</p>';
            } else {
                $errorSearch = '<p class="error">Beim Entsperren ist ein Fehler aufgetreten!</p>';
            }
        }
        searchAdmin($dataRights["rights"]);
    } elseif ($_POST['edit'] == 'Random Password') {
        searchAdmin($dataRights["rights"]);
        confirmRandomPassword();
    } elseif ($_POST['confirmRandomPassword'] == 'Yes') {
        userRandomPassword();
        searchAdmin($dataRights["rights"]);
    } elseif ($_POST['confirmRandomPassword'] == 'No') {
        global $errorSearch;
        $errorSearch = '<p class="error">Das Passwort wurde nicht geändert!</p>';
        searchAdmin($dataRights["rights"]);
    } elseif ($_POST['submit'] == 'User Anlegen') {
        newTableRow("userdata");
    } elseif ($_POST['edit'] == 'Online/Offline') {
        global $errorSearch;
        if ($_POST["unlocked"] == 0) {
            $unlocked = 1;
            if (database::getConnections()->updateStatusNLP("nlp", $_POST["userId"], $unlocked) == true) {
                $errorSearch = '<p class="success">Erfolgreich Aktiviert!</p>';
            } else {
                $errorSearch = '<p class="error">Beim aktivieren ist ein Fehler aufgetreten!</p>';
            }
        } else {
            $unlocked = 0;
            if (database::getConnections()->updateStatusNLP("nlp", $_POST["userId"], $unlocked) == true) {
                $errorSearch = '<p class="success">Erfolgreich Deaktiviert!</p>';
            } else {
                $errorSearch = '<p class="error">Beim deaktivieren ist ein Fehler aufgetreten!</p>';
            }
        }
        searchNLP();
    } elseif ($_POST['submit'] == 'Nlp Anlegen') {
        newTableRow("nlp");
    } elseif ($_POST['edit'] == 'Registrieren') {
        global $errorSearch;
        insertTableRow("userdata");
        echo $errorSearch;
    } elseif ($_POST['edit'] == 'Importieren') {
        global $errorSearch;
        insertTableRow($_POST["tableName"]);
        echo $errorSearch;
    }

// Schaut welche Rechte der User hat und gibt dann genau dass aus
} elseif ($dataRights["rights"] >= 50) {
    // Gib die Buttons aus
    echo '<br><div class="formAdminMenu"><form method="post" enctype="multipart/form-data">
        <input class="btn btnPrimary" type="submit" name="submit" value="Admin User"><br>
    </form></div><br>';

    // Wenn die $_POSTS nicht existieren, dann mache sie leer
    if (!isset($_POST['submit'])) {
        $_POST['submit'] = "";
    }

    if (!isset($_POST['xsrfToken'])) {
        $_POST['xsrfToken'] = "";
    }

    if (!isset($_POST['edit'])) {
        $_POST['edit'] = "";
    }

    if (!isset($_POST['confirmDelete'])) {
        $_POST['confirmDelete'] = "";
    }

    if (!isset($_POST['confirmRandomPassword'])) {
        $_POST['confirmRandomPassword'] = "";
    }

    // Viele If und Elseif abfragen. Es wird geschaut, welchen Button der User geklickt hat und dann soll genau diese Funktion aufgerufen werden
    if ($_POST['submit'] == 'Admin User') {
        searchAdmin($dataRights["rights"]);
    } elseif ($_POST['submit'] == 'User Suchen') {
        searchAdmin($dataRights["rights"]);
        tableShowUsers();
    } elseif ($_POST['edit'] == 'Delete') {
        searchAdmin($dataRights["rights"]);
        deleteTableRow();
    } elseif ($_POST['confirmDelete'] == 'Yes') {
        global $errorSearch;
        if (database::getConnections()->deleteByID($_POST["tableName"], $_POST["userId"]) == true && database::getConnections()->deleteByUSERID("userinfo", $_POST["userId"]) == true) {
            $errorSearch = '<p class="success">Die Reihe wurde erfolgreich gelöscht!</p>';
        } else {
            $errorSearch = '<p class="error">Es ist ein fehler aufgetreten und die Reihe konnte nicht gelöscht werden!</p>';
        }
        searchAdmin($dataRights["rights"]);
    } elseif ($_POST['confirmDelete'] == 'No') {
        global $errorSearch;
        $errorSearch = '<p class="error">Die Reihe wurde nicht gelöscht!</p>';
        searchAdmin($dataRights["rights"]);
    } elseif ($_POST['edit'] == 'Ent/Sperren') {
        global $errorSearch;
        if (empty($_POST["status"])) {
            $status = 1;
            if (database::getConnections()->updateStatusAdmin("userinfo", $_POST["userId"], $status) == true) {
                $errorSearch = '<p class="success">Erfolgreich gesperrt!</p>';
            } else {
                $errorSearch = '<p class="error">Beim Sperren ist ein Fehler aufgetreten!</p>';
            }
        } else {
            $status = 0;
            if (database::getConnections()->updateStatusAdmin("userinfo", $_POST["userId"], $status) == true) {
                $errorSearch = '<p class="success">Erfolgreich entsperrt!</p>';
            } else {
                $errorSearch = '<p class="error">Beim Entsperren ist ein Fehler aufgetreten!</p>';
            }
        }
        searchAdmin($dataRights["rights"]);
    } elseif ($_POST['edit'] == 'Random Password') {
        searchAdmin($dataRights["rights"]);
        confirmRandomPassword();
    } elseif ($_POST['confirmRandomPassword'] == 'Yes') {
        userRandomPassword();
        searchAdmin($dataRights["rights"]);
    } elseif ($_POST['confirmRandomPassword'] == 'No') {
        global $errorSearch;
        $errorSearch = '<p class="error">Das Passwort wurde nicht geändert!</p>';
        searchAdmin($dataRights["rights"]);
    }

// Schaut welche Rechte der User hat und gibt dann genau dass aus
} elseif ($dataRights["rights"] >= 30) {
    // Gib die Buttons aus
    echo '<br><div class="formAdminMenu"><form method="post" enctype="multipart/form-data">
            <input class="btn btnPrimary" type="submit" name="submit" value="Admin Nlp"><br>
            <input class="btn btnPrimary" type="submit" name="submit" value="Nlp Anlegen"><br>
        </form></div><br>';

    // Wenn die $_POSTS nicht existieren, dann mache sie leer
    if (!isset($_POST['submit'])) {
        $_POST['submit'] = "";
    }

    if (!isset($_POST['xsrfToken'])) {
        $_POST['xsrfToken'] = "";
    }

    if (!isset($_POST['edit'])) {
        $_POST['edit'] = "";
    }

    if (!isset($_POST['confirmDelete'])) {
        $_POST['confirmDelete'] = "";
    }

    // Viele If und Elseif abfragen. Es wird geschaut, welchen Button der User geklickt hat und dann soll genau diese Funktion aufgerufen werden
    if ($_POST['submit'] == 'Admin Nlp') {
        searchNLP();
    } elseif ($_POST['submit'] == 'NLP Suchen') {
        searchNLP();
        tableShowNlp();
    } elseif ($_POST['edit'] == 'Edit') {
        searchNLP();
        editTableRow($_POST["tableName"]);
    } elseif ($_POST['edit'] == 'Aktualisieren') {
        global $errorSearch;
        searchNLP();
        updateTableRow($_POST["tableName"]);
        echo $errorSearch;
    } elseif ($_POST['edit'] == 'Show') {
        global $errorSearch;
        $errorSearch = '<p class="success">Erfolgreich!</p>';
        searchNLP();
        showTableRow("nlp");
    } elseif ($_POST['edit'] == 'Delete') {
        searchNLP();
        deleteTableRow();
    } elseif ($_POST['confirmDelete'] == 'Yes') {
        global $errorSearch;
        if (database::getConnections()->deleteByID($_POST["tableName"], $_POST["userId"]) == true) {
            $errorSearch = '<p class="success">Die Reihe wurde erfolgreich gelöscht!</p>';
        } else {
            $errorSearch = '<p class="error">Es ist ein fehler aufgetreten und die Reihe konnte nicht gelöscht werden!</p>';
        }
        searchNLP();
    } elseif ($_POST['confirmDelete'] == 'No') {
        global $errorSearch;
        $errorSearch = '<p class="error">Die Reihe wurde nicht gelöscht!</p>';
        searchNLP();
    } elseif ($_POST['edit'] == 'Online/Offline') {
        global $errorSearch;
        if ($_POST["unlocked"] == 0) {
            $unlocked = 1;
            if (database::getConnections()->updateStatusNLP("nlp", $_POST["userId"], $unlocked) == true) {
                $errorSearch = '<p class="success">Erfolgreich Aktiviert!</p>';
            } else {
                $errorSearch = '<p class="error">Beim aktivieren ist ein Fehler aufgetreten!</p>';
            }
        } else {
            $unlocked = 0;
            if (database::getConnections()->updateStatusNLP("nlp", $_POST["userId"], $unlocked) == true) {
                $errorSearch = '<p class="success">Erfolgreich Deaktiviert!</p>';
            } else {
                $errorSearch = '<p class="error">Beim deaktivieren ist ein Fehler aufgetreten!</p>';
            }
        }

        searchNLP();
    } elseif ($_POST['submit'] == 'Nlp Anlegen') {
        newTableRow("nlp");
    } elseif ($_POST['edit'] == 'Importieren') {
        global $errorSearch;
        insertTableRow($_POST["tableName"]);
        echo $errorSearch;
    }
}

// Funktion, die das Formular zum NLP Übungen Suchen anzeigt
function searchNLP()
{
    $errorSearch = "";
    echo '<div class="formAdminSearchMenu"><form method="post" enctype="multipart/form-data">
        <input type="hidden" name="tableName" value="nlp">
        <h1>Admin NLP Suche</h1>
        <p>Bist du ein Moderator, dann siehst du alle Einträge, bist du ein NLP Autor, dann siehst du nur deine eigenen Beiträge!</p>
        <label for="search">Eingabe:</label>
        <input class="" type="text" name="search" id="search" placeholder="Wort/Name/Buchstaben" value="">
        <br>
        <input class="btn btnSecondary" type="submit" name="submit" value="NLP Suchen">
    </form></div><hr><br>';
    global $errorSearch;
    echo $errorSearch;
}
// Funktion, die das Formular zum User Suchen anzeigt
function searchAdmin($rights)
{

    if ($rights >= 60) {
        $errorSearch = "";
        echo '<div class="formAdminSearchMenu"><form method="post" enctype="multipart/form-data">
            <input type="hidden" name="tableName" value="userdata">
            <h1>Admin User Suche</h1>
            <label for="search">Eingabe:</label>
            <input class="" type="text" name="search" id="search" placeholder="Wort/Name/Email/Buchstaben" value="">
            <br>
            <input class="btn btnSecondary" type="submit" name="submit" value="User Suchen">
        </form></div><hr><br>';
        global $errorSearch;
        echo $errorSearch;
    } elseif ($rights >= 50) {
        $errorSearch = "";
        echo '<div class="formAdminSearchMenu"><form method="post" enctype="multipart/form-data">
            <input type="hidden" name="tableName" value="user">
            <h1>Admin User Suche</h1>
            <label for="search">Eingabe:</label>
            <input class="" type="text" name="search" placeholder="Wort/Name/Email/Buchstaben" value="">
            <br>
            <input class="btn btnSecondary" type="submit" name="submit" value="User Suchen">
        </form></div><hr><br>';
        global $errorSearch;
        echo $errorSearch;
    }
}
// Funktion, die die Tabelle der ganzen User ausgibt
function tableShowUsers()
{

    global $newUser, $userid, $dataRights;
    $input = sanitize_input($_POST["search"]);

    $searchdata = database::getConnections()->viewAdminUsersSearch($input, $dataRights['rights']);

    if ($dataRights["rights"] >= 90) {
        $form = '<div class="tableWrapper"><table class="table table-bordered table-hover tablesaw tablesaw-stack" data-tablesaw-sortable data-tablesaw-sortable-switch data-tablesaw-mode="stack">
    <h1>Du hast nach " ' . $input . ' " gesucht!</h1>
    <thead><tr>
    <th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>ID</th>
    <th data-tablesaw-sortable-col>VORNAME</th>
    <th data-tablesaw-sortable-col>NACHNAME</th>
    <th data-tablesaw-sortable-col>E-MAIL</th>
    <th data-tablesaw-sortable-col>LOGIN</th>
    <th data-tablesaw-sortable-col data-tablesaw-sortable-numeric>RECHTE</th>
    <th data-tablesaw-sortable-col data-tablesaw-sortable-numeric>STATUS</th>
    <th>BEARBEITEN</th>
    </tr></thead><tbody>';

        foreach ($searchdata as $entry) {
            $form .= '<tr>
        <td>' . $entry['id'] . '</td>
        <td>' . $entry['firstname'] . '</td>
        <td>' . $entry['lastname'] . '</td>
        <td>' . $entry['email'] . '</td>
        <td>' . $entry['lastlogin'] . '</td>
        <td>' . $entry['rights'] . '</td>
        <td>' . $entry['status'] . '</td>
        <td><form action="" method="post">
        <input type="hidden" name="userId" value="' . $entry['userid'] . '">
        <input type="hidden" name="tableName" value="userdata">
        <input type="hidden" name="status" value="' . $entry['status'] . '">
        <input type="hidden" name="rights" value="' . $entry['rights'] . '">
        <input class="btnAdmin" type="submit" name="edit" value="Show">
        <input class="btnAdmin" type="submit" name="edit" value="Edit">
        <input class="btnAdmin" type="submit" name="edit" value="Delete">
        <input class="btnAdmin" type="submit" name="edit" value="Ent/Sperren">
        <input class="btnAdmin" type="submit" name="edit" value="Random Password">
        </form></td></tr>';
        }

        $form .= '</tbody></table></div>';
        echo $form;

    } elseif ($dataRights["rights"] >= 60) {

        $form = '<div class="tableWrapper"><table class="table table-bordered table-hover tablesaw tablesaw-stack" data-tablesaw-sortable data-tablesaw-sortable-switch data-tablesaw-mode="stack">
        <h1>Du hast nach " ' . $input . ' " gesucht!</h1>
        <thead><tr>
        <th data-tablesaw-sortable-col>VORNAME</th>
        <th data-tablesaw-sortable-col>NACHNAME</th>
        <th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>E-MAIL</th>
        <th data-tablesaw-sortable-col>LOGIN</th>
        <th data-tablesaw-sortable-col data-tablesaw-sortable-numeric>STATUS</th>
        <th>BEARBEITEN</th>
        </tr></thead><tbody>';

        foreach ($searchdata as $entry) {
            $form .= '<tr>
            <td>' . $entry['firstname'] . '</td>
            <td>' . $entry['lastname'] . '</td>
            <td>' . $entry['email'] . '</td>
            <td>' . $entry['lastlogin'] . '</td>
            <td>' . $entry['status'] . '</td>
            <td><form action="" method="post">
            <input type="hidden" name="userId" value="' . $entry['userid'] . '">
            <input type="hidden" name="tableName" value="userdata">
            <input type="hidden" name="status" value="' . $entry['status'] . '">
            <input class="btnAdmin" type="submit" name="edit" value="Delete">
            <input class="btnAdmin" type="submit" name="edit" value="Ent/Sperren">
            <input class="btnAdmin" type="submit" name="edit" value="Random Password">
            </form></td></tr>';
        }

        $form .= '</tbody></table></div>';
        echo $form;

    } elseif ($dataRights["rights"] >= 50) {

        $form = '<div class="tableWrapper"><table class="table table-bordered table-hover tablesaw tablesaw-stack" data-tablesaw-sortable data-tablesaw-sortable-switch data-tablesaw-mode="stack">
            <h1>Du hast nach " ' . $input . ' " gesucht!</h1>
            <thead><tr>
            <th data-tablesaw-sortable-col>NACHNAME</th>
            <th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>Email</th>
            <th data-tablesaw-sortable-col>LOGIN</th>
            <th data-tablesaw-sortable-col>REGISTERED</th>
            <th data-tablesaw-sortable-col data-tablesaw-sortable-numeric>STATUS</th>
            <th>BEARBEITEN</th>
            </tr></thead><tbody>';

        foreach ($searchdata as $entry) {
            $form .= '<tr>
                <td>' . $entry['lastname'] . '</td>
                <td>' . $entry['email'] . '</td>
                <td>' . $entry['lastlogin'] . '</td>
                <td>' . $entry['registered'] . '</td>
                <td>' . $entry['status'] . '</td>
                <td><form action="" method="post">
                <input type="hidden" name="userId" value="' . $entry['userid'] . '">
                <input type="hidden" name="tableName" value="userdata">
                <input type="hidden" name="status" value="' . $entry['status'] . '">
                <input class="btnAdmin" type="submit" name="edit" value="Ent/Sperren">
                </form></td></tr>';
        }

        $form .= '</tbody></table></div>';
        echo $form;

    } else {
        echo '<div class="error">Es ist ein Fehler aufgetreten, bitte versuchen Sie es erneut! Falls es dann immer noch nicht klappt, wenden Sie sich an den Besitzer und den Backend Developer! support@boost-your-life.de</div>';
    }
}
// Funktion, die die Tabelle der ganzen NLP Übungen ausgibt
function tableShowNlp()
{

    global $newUser, $userid, $dataRights;
    $input = sanitize_input($_POST["search"]);

    $searchdata = database::getConnections()->viewNLPSearch($input, $userid, $dataRights["rights"]);

    $form = '<div class="tableWrapper"><table class="table table-bordered table-hover tablesaw tablesaw-stack" data-tablesaw-sortable data-tablesaw-sortable-switch data-tablesaw-mode="stack">
    <h1>Du hast nach " ' . $input . ' " gesucht!</h1>
    <thead><tr>
    <th data-tablesaw-sortable-col data-tablesaw-sortable-numeric data-tablesaw-sortable-default-col>ONLINE</th>
    <th data-tablesaw-sortable-col>UPLOADED</th>
    <th data-tablesaw-sortable-col>TITEL</th>
    <th data-tablesaw-sortable-col>BESCHREIBUNG</th>
    <th data-tablesaw-sortable-col>TEXT</th>
    <th data-tablesaw-sortable-col>GENRE</th>
    <th data-tablesaw-sortable-col>SOUNDCLOUD</th>
    <th data-tablesaw-sortable-col>YOUTUBE</th>
    <th>BEARBEITEN</th>
    </tr></thead><tbody>';

    if ($dataRights["rights"] >= 40) {

        foreach ($searchdata as $entry) {
            $form .= '<tr>
            <td>' . $entry['unlocked'] . '</td>
            <td>' . $entry['uploaded'] . '</td>
            <td>' . $entry['title'] . '</td>
            <td>' . $entry['description'] . '</td>
            <td>' . $entry['text'] . '</td>
            <td>' . $entry['genre'] . '</td>
            <td>' . $entry['soundcloud'] . '</td>
            <td>' . $entry['youtube'] . '</td>
            <td><form action="" method="post">
            <input type="hidden" name="userId" value="' . $entry['id'] . '">
            <input type="hidden" name="tableName" value="nlp">
            <input type="hidden" name="unlocked" value="' . $entry['unlocked'] . '">
            <input class="btnAdmin" type="submit" name="edit" value="Show">
            <input class="btnAdmin" type="submit" name="edit" value="Edit">
            <input class="btnAdmin" type="submit" name="edit" value="Delete">
            <input class="btnAdmin" type="submit" name="edit" value="Online/Offline">
            </form></td></tr>';
        }

    } elseif ($dataRights["rights"] >= 30) {

        foreach ($searchdata as $entry) {
            $form .= '<tr>
            <td>' . $entry['unlocked'] . '</td>
            <td>' . $entry['uploaded'] . '</td>
            <td>' . $entry['title'] . '</td>
            <td>' . $entry['description'] . '</td>
            <td>' . $entry['text'] . '</td>
            <td>' . $entry['genre'] . '</td>
            <td>' . $entry['soundcloud'] . '</td>
            <td>' . $entry['youtube'] . '</td>
            <td><form action="" method="post">
            <input type="hidden" name="userId" value="' . $entry['id'] . '">
            <input type="hidden" name="tableName" value="nlp">
            <input class="btnAdmin" type="submit" name="edit" value="Show">
            <input class="btnAdmin" type="submit" name="edit" value="Edit">
            <input class="btnAdmin" type="submit" name="edit" value="Delete">
            </form></td></tr>';
        }

    }

    $form .= '</tbody></table></div>';
    echo $form;

}
// Funktion, damit ein Formular angezeigt wird um es dann zu Editieren
function editTableRow($tableName)
{

    if ($tableName == "userdata") {
        $errorUpdate = $errorFirstName = $errorLastName = $errorEmail = "";
        global $dataRights;
        //$result = database::getConnections()->viewByID($_POST["tableName"], $_POST["userId"]);
        $result = database::getConnections()->view('userdata INNER JOIN userinfo ON userdata.id=userinfo.userid', 'userdata.id=:userid', [':userid' => $_POST["userId"]]);

        $tokenClass = new token();
        $xsrfType = "AdminUpdateUserForm";
        $xsrfExpires = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +8 minutes"));

        $xsrfToken = $tokenClass->generateXSRFToken();
        database::getConnections()->tokenInsert($xsrfToken, $xsrfType, $xsrfExpires, $xsrfId = null);

        if ($dataRights["rights"] >= 90) {
            global $errorUpdate;
            echo '<div class="formAdminUpdate"><form method="post" enctype="multipart/form-data">
            <input type="hidden" name="userId" value="' . $result['userid'] . '">
            <input type="hidden" name="tableName" value="userdata">
            <input type="hidden" name="rights" value="' . $result['rights'] . '">
            <input type="hidden" name="xsrfToken" value="' . $xsrfToken . '">
                <div>
                    <div>' . $errorUpdate . '</div>
                    <h1>Editieren</h1>
                </div>
                <div class="field">
                    <label for="firstname">Vorname:</label>
                    <input type="text" name="firstname" id="firstname" placeholder="Max" autofocus maxlength="75" value="' . $result['firstname'] . '">
                    <div>' . $errorFirstName . '</div>
                </div>
                <div class="field">
                    <label for="lastname">Nachname:</label>
                    <input type="text" name="lastname" id="lastname" placeholder="Mustermann" maxlength="75" value="' . $result['lastname'] . '">
                    <div>' . $errorLastName . '</div>
                </div>
                <div class="field">
                    <label for="email">E-Mail Adresse:</label>
                    <input type="email" name="email" id="email" placeholder="max-Mustermann@gmail.com" maxlength="255" value="' . $result['email'] . '">
                    <div>' . $errorEmail . '</div>
                </div>
                <div class="field">
                <label for="rights">Rechte:</label>
                    <select class="" name="editRights">
				        <optgroug name="editRights">
                            <option value="" disabled selected>' . $result['rights'] . '</option>
                            <option value="0">Freie Nutzung/User</option>
                            <option value="10">Vollversion/User</option>
                            <option value="20">NLP Coach</option>
                            <option value="30">NLP Moderator</option>
                            <option value="50">Support</option>
                            <option value="60">Admin</option>
                            <option value="99">Besitzer</option>
				        </optgroup>
			        </select>
                </div>

                <input class="btn btnSecondary" type="submit" name="edit" value="Aktualisieren">
            </form></div><br>';
        }

    } else {
        $errorUpdate = $errorAutor = $errorTitle = $errorDescription = $errorTextMessage = $errorSoundcloudLink = $errorYouTubeLink = $errorImage = "";
        global $dataRights, $userid;

        $result = database::getConnections()->viewByID($_POST["tableName"], $_POST["userId"]);

        $tokenClass = new token();
        $xsrfType = "AdminUpdateNLPForm";
        $xsrfExpires = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +8 minutes"));

        $xsrfToken = $tokenClass->generateXSRFToken();
        database::getConnections()->tokenInsert($xsrfToken, $xsrfType, $xsrfExpires, $xsrfId = null);

        $profilPic = database::getConnections()->view("images", "nlpid=:nlpid AND tablename=:tablename", [':nlpid' => $_POST["userId"], ':tablename' => 'Nlp']);


        if ($dataRights["rights"] >= 30) {
            global $errorUpdate;
            echo '<div class="formAdminUpdate"><form method="post" enctype="multipart/form-data">
            <input type="hidden" name="userId" value="' . $result['id'] . '">
            <input type="hidden" name="tableName" value="nlp">
            <input type="hidden" name="valueNlpImage" value="' . $profilPic['src'] . '">
            <input type="hidden" name="valueGenre" value="' . $result['genre'] . '">
            <input type="hidden" name="unlocked" value="' . $result['unlocked'] . '">
            <input type="hidden" name="xsrfToken" value="' . $xsrfToken . '">
                <div>
                    <div>' . $errorUpdate . '</div>
                    <h1>Editieren</h1>
                </div>
                <div class="field">
                    <label for="nlpImage">Übungsbild:</label>
                    <input type="file" accept="image/*" name="appImage" id="nlpImage">
                    <div>' . $errorImage . '</div>
                </div>
                <div class="field">
                    <label for="title">Titel:</label>
                    <input type="text" name="title" id="title" placeholder="Titel" autofocus maxlength="75" value="' . $result['title'] . '">
                    <div>' . $errorTitle . '</div>
                </div>
                <div class="field">
                    <label for="description">Beschreibung:</label>
                    <input name="description" id="description" placeholder="Beschreibung" maxlength="150" value="' . $result['description'] . '">
                    <div>' . $errorDescription . '</div>
                </div>
                <div class="field">
                    <label for="text">Text:</label>
                    <textarea name="text" id="text" placeholder="Text" rows="4" cols="60" value="">' . $result['text'] . '</textarea>
                    <div>' . $errorTextMessage . '</div>
                </div>
                <div class="field">
                <label for="rights">Genre:</label>
                    <select class="" name="editGenre">
				        <optgroug name="editGenre">
                            <option value="" disabled selected>' . $result['genre'] . '</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            <option value="F">F</option>
                            <option value="G">G</option>
				        </optgroup>
			        </select>
                </div>
                <div class="field">
                    <label for="soundcloudLink">Soundcloud Link:</label>
                    <input type="url" name="soundcloudLink" id="soundcloudLink" placeholder="Soundcloud Link" maxlength="255" value="' . $result['soundcloud'] . '">
                    <div>' . $errorSoundcloudLink . '</div>
                </div>
                <div class="field">
                    <label for="youtubeLink">YouTube Link:</label>
                    <input type="url" name="youtubeLink" id="youtubeLink" placeholder="YouTube Link" maxlength="255" value="' . $result['youtube'] . '">
                    <div>' . $errorYouTubeLink . '</div>
                </div>
                <div class="field">
                <label for="editUnlocked">Freischalten:</label>
                    <select class="" name="editUnlocked">
                        <optgroug name="editUnlocked">
                            <option value="' . $result['unlocked'] . '" disabled selected>' . $result['unlocked'] . '</option>
                            <option value="1">ONLINE</option>
                            <option value="0">OFFLINE</option>
                        </optgroup>
                    </select>
                </div>

                <input class="btn btnSecondary" type="submit" name="edit" value="Aktualisieren">
            </form></div><br>';
        } else {
            global $errorUpdate;
            echo '<div class="formAdminUpdate"><form method="post" enctype="multipart/form-data">
            <input type="hidden" name="userId" value="' . $result['id'] . '">
            <input type="hidden" name="tableName" value="nlp">
            <input type="hidden" name="valueNlpImage" value="' . $profilPic['src'] . '">
            <input type="hidden" name="valueGenre" value="' . $result['genre'] . '">
            <input type="hidden" name="unlocked" value="' . $result['unlocked'] . '">
            <input type="hidden" name="xsrfToken" value="' . $xsrfToken . '">
                <div>
                    <div>' . $errorUpdate . '</div>
                    <h1>Editieren</h1>
                </div>
                <div class="field">
                    <label for="title">Titel:</label>
                    <input type="text" name="title" id="title" placeholder="Titel" autofocus maxlength="75" value="' . $result['title'] . '">
                    <div>' . $errorTitle . '</div>
                </div>
                <div class="field">
                    <label for="description">Beschreibung:</label>
                    <input name="description" id="description" placeholder="Beschreibung" maxlength="150" value="' . $result['description'] . '">
                    <div>' . $errorDescription . '</div>
                </div>
                <div class="field">
                    <label for="text">Text:</label>
                    <textarea name="text" id="text" placeholder="Text" value="">' . $result['text'] . '</textarea>
                    <div>' . $errorTextMessage . '</div>
                </div>
                <div class="field">
                <label for="rights">Genre:</label>
                    <select class="" name="editGenre">
				        <optgroug name="editGenre">
                            <option value="" disabled selected>' . $result['genre'] . '</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            <option value="F">F</option>
                            <option value="G">G</option>
				        </optgroup>
			        </select>
                </div>
                <div class="field">
                    <label for="title">Soundcloud Link:</label>
                    <input type="url" name="soundcloudLink" id="soundcloudLink" placeholder="Soundcloud Link" maxlength="255" value="' . $result['soundcloud'] . '">
                    <div>' . $errorSoundcloudLink . '</div>
                </div>
                <div class="field">
                    <label for="title">YouTube Link:</label>
                    <input type="url" name="youtubeLink" id="youtubeLink" placeholder="YouTube Link" maxlength="255" value="' . $result['youtube'] . '">
                    <div>' . $errorYouTubeLink . '</div>
                </div>
                <input class="btn-input" type="submit" name="edit" value="Aktualisieren">
            </form></div><br>';
        }
    }

}
// Funktion, damit man das Formular Updaten kann, diese Sachen werden auch in der Datenbank aktualisert
function updateTableRow($tableName)
{

    if ($tableName == "userdata") {
        $errorUpdate = $errorFirstName = $errorLastName = $errorEmail = "";
        global $dataRights, $errorFirstName, $errorLastName, $errorEmail, $errorPassword, $errorPassword, $errorPasswordRepeat, $errorUpdate, $errorAGBCheckbox;

        if (!isset($_POST['rights'])) {
            $_POST['rights'] = "";
        }

        if (!isset($_POST['editRights'])) {
            $_POST['editRights'] = "";
        }

        $tokenClass = new token();
        $xsrfType = "AdminUpdateUserForm";

        //$result = database::getConnections()->view("user", "id=:id", [':id' => $_POST["userId"]]);
        $result = database::getConnections()->view('userdata INNER JOIN userinfo ON userdata.id=userinfo.userid', 'userdata.id=:userid', [':userid' => $_POST["userId"]]);

        $firstnameInput = sanitize_input(ucfirst($_POST['firstname']));
        $lastnameInput = sanitize_input(ucfirst($_POST['lastname']));
        $emailInput = sanitize_input($_POST['email']);

        $validation = new validation();
        $updateValidation = $validation->validProfile($firstnameInput, $lastnameInput, $emailInput);

        if ($updateValidation == true) {
            $data = database::getConnections()->view("userdata", "email=:email", [':email' => $emailInput]);
            if ($data == false || $data["email"] == $emailInput) {
                if (($tokenData = $tokenClass->checkXSRFToken($_POST["xsrfToken"])) == true) {

                    $updateData = database::getConnections()->updateAdminUserData("userdata", $_POST["userId"], $firstnameInput, $lastnameInput, $emailInput, 0);

                    if ($_POST['editRights'] == "") {
                        $updateInfo = database::getConnections()->updateAdminUserData("userinfo", $_POST["userId"], $firstnameInput, $lastnameInput, $emailInput, $_POST['rights']);
                    } elseif (!empty($_POST['editRights'])) {
                        $updateInfo = database::getConnections()->updateAdminUserData("userinfo", $_POST["userId"], $firstnameInput, $lastnameInput, $emailInput, $_POST['editRights']);
                    } else {
                        $updateInfo = database::getConnections()->updateAdminUserData("userinfo", $_POST["userId"], $firstnameInput, $lastnameInput, $emailInput, 0);
                    }

                    if ($updateData == true && $updateInfo) {
                        global $errorSearch;
                        $errorSearch = '<p class="success">Die Daten konnten aktualisiert werden!</p>';
                    } else {
                        global $errorSearch;
                        echo $errorSearch = '<p class="error">Die Daten konnten nicht aktualisiert werden, ein Fehler ist aufgetreten. Bitte erneut versuchen!</p>';
                    }

                    $tokenClass->deleteXSRFToken($tokenData["id"], $xsrfType);
                    //$result = database::getConnections()->view("user", "id=:id", [':id' => $_POST["userId"]]);
                } else {
                    global $errorSearch;
                    $errorSearch = '<p class="error">Bitte versuchen Sie es nach einem Refresh erneut!</p>';
                }

            } else {
                global $errorSearch;
                $errorSearch = '<p class="errorMessages">Diese E-Mail Adresse ist schon vergeben, bitte eine andere eingeben!</p>';
            }
        } else {
            if ($dataRights["rights"] >= 90) {
                global $errorUpdate, $errorFirstName, $errorLastName, $errorEmail;
                echo '<div class="formAdminUpdate"><form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="userId" value="' . $result['userid'] . '">
                        <input type="hidden" name="tableName" value="userdata">
                        <input type="hidden" name="rights" value="' . $result['rights'] . '">
                        <input type="hidden" name="xsrfToken" value="' . $_POST["xsrfToken"] . '">
                            <div>
                                <div>' . $errorUpdate . '</div>
                                <h1>Editieren</h1>
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
                                <input type="text" name="email" id="email" placeholder="max-Mustermann@gmail.com" maxlength="255" value="' . $result['email'] . '">
                                <div>' . $errorEmail . '</div>
                            </div>
                            <div class="field">
                            <label for="rights">Rechte:</label>
                            <select class="col s-12" name="rights">
                                <optgroug name="rights">
                                    <option value="" disabled selected>' . $result['rights'] . '</option>
                                    <option value="0">Freie Nutzung/User</option>
                                    <option value="10">Vollversion/User</option>
                                    <option value="20">NLP Coach</option>
                                    <option value="30">NLP Moderator</option>
                                    <option value="50">Support</option>
                                    <option value="60">Admin</option>
                                    <option value="99">Besitzer</option>
                                </optgroup>
                            </select>
                        </div>
                        <input class="btn btnSecondary" type="submit" name="edit" value="Aktualisieren">
                        </form></div><br>';
            }
        }
    } else {
        $errorUpdate = $errorAutor = $errorTitle = $errorDescription = $errorTextMessage = $errorSoundcloudLink = $errorYouTubeLink = "";

        if (!isset($_POST['editUnlocked'])) {
            $_POST['editUnlocked'] = "";
        }

        global $dataRights, $userid;

        $titleInput = sanitize_input(ucfirst($_POST['title']));
        $descriptionInput = sanitize_input(ucfirst($_POST['description']));
        $textInput = sanitize_input($_POST['text']);
        $soundcloudInput = sanitize_input($_POST['soundcloudLink']);
        $youtubeInput = sanitize_input($_POST['youtubeLink']);

        $tokenClass = new token();
        $xsrfType = "AdminUpdateNLPForm";

        $result = database::getConnections()->view("nlp", "id=:id", [':id' => $_POST["userId"]]);

        $validation = new validation();
        $updateValidation = $validation->validUpdateNLP($titleInput, $descriptionInput, $textInput, $soundcloudInput, $youtubeInput);

        if ($updateValidation == true) {
            $data = database::getConnections()->view("nlp", "id=:id", [':id' => $_POST["userId"]]);
            if ($data == true) {
                if (($tokenData = $tokenClass->checkXSRFToken($_POST["xsrfToken"])) == true) {

                    $imageData = database::getConnections()->view("images", "nlpid=:nlpid", [':nlpid' => $userid]);

                    // Es wird geschaut ob ein Bild hochgeladen wurde
                    if (!$_FILES['appImage']['size'] == 0) {

                        require_once '../functions/imageUpload.php';

                        $unlink = "..//" . $_POST['valueNlpImage'];
                        $bildFunktion = imageUploads("update", "Nlp", $userid, $result["id"], $unlink);
                        // $nlpData = database::getConnections()->view("nlp", "userid=:userid ORDER BY uploaded DESC LIMIT 1", [':userid' => $userid]);
                        $imageData = database::getConnections()->view("images", "userid=:userid ORDER BY uploaded DESC LIMIT 1", [':userid' => $userid]);

                        database::getConnections()->updateNlpImageID($_POST["userId"], $imageData["id"]);
                    }

                    if (empty($_POST['editUnlocked'])) {
                        $unlocked = 0;
                    } else {
                        $unlocked = 1;
                    }

                    if (empty($_POST['editGenre'])) {
                        $genre = $_POST['valueGenre'];
                    } else {
                        $genre = $_POST['editGenre'];
                    }

                    $updateData = database::getConnections()->updateDataNlp("nlp", $_POST["userId"], $titleInput, $descriptionInput, $textInput, $genre, $soundcloudInput, $youtubeInput, $unlocked);

                    if ($updateData == true) {
                        global $errorSearch;
                        $errorSearch = '<p class="success">Die Daten konnten aktualisiert werden!</p>';
                    } else {
                        global $errorSearch;
                        echo $errorSearch = '<p class="error">Die Daten konnten nicht aktualisiert werden, ein Fehler ist aufgetreten. Bitte erneut versuchen!</p>';
                    }

                    $tokenClass->deleteXSRFToken($tokenData["id"], $xsrfType);
                    $result = database::getConnections()->view("nlp", "id=:id", [':id' => $_POST["userId"]]);
                } else {
                    global $errorSearch;
                    $errorSearch = '<p class="error">Bitte versuchen Sie es nach einem Refresh erneut! Wenn nach dem Refresh das Problem immer noch besteht, so wenden Sie sich bitte an den Support ===> support@boost-your-life.de</p>';
                }

            } else {
                global $errorSoundcloudLink, $errorYouTubeLink;
                $errorSoundcloudLink = $errorYouTubeLink = '<p class="errorMessages">Der YouTube oder Soundcloud Link wurden schon mal verwendet, bitte gebe einen anderen Link an!</p>';
            }
        } elseif ($data == false || $tokenData == false || $data["soundcloud"] == $soundcloudInput || $data["youtube"] == $youtubeInput) {
            if ($dataRights["rights"] >= 30) {
                global $profilPic, $errorUpdate, $errorAutor, $errorTitle, $errorDescription, $errorTextMessage, $errorSoundcloudLink, $errorYouTubeLink;
                echo '<div class="formAdminUpdate"><form method="post" enctype="multipart/form-data">
                <input type="hidden" name="userId" value="' . $result['id'] . '">
                <input type="hidden" name="tableName" value="nlp">
                <input type="hidden" name="valueNlpImage" value="' . $profilPic['src'] . '">
                <input type="hidden" name="valueGenre" value="' . $result['genre'] . '">
                <input type="hidden" name="unlocked" value="' . $result['unlocked'] . '">
                <input type="hidden" name="xsrfToken" value="' . $_POST["xsrfToken"] . '">
                    <div>
                        <div>' . $errorUpdate . '</div>
                        <h1>Editieren</h1>
                    </div>
                    <div class="field">
                        <label for="nlpImage">Übungsbild:</label>
                        <input type="file" accept="image/*" name="appImage" id="nlpImage">
                        <div>' . $errorImage . '</div>
                    </div>
                    <div class="field">
                        <label for="title">Titel:</label>
                        <input type="text" name="title" id="title" placeholder="Titel" autofocus maxlength="75" value="' . $result['title'] . '">
                        <div>' . $errorTitle . '</div>
                    </div>
                    <div class="field">
                        <label for="description">Beschreibung:</label>
                        <input name="description" id="description" placeholder="Beschreibung" maxlength="75" value="' . $result['description'] . '">
                        <div>' . $errorDescription . '</div>
                    </div>
                    <div class="field">
                        <label for="text">Text:</label>
                        <textarea name="text" id="text" placeholder="Text" rows="4" cols="60" value="">' . $result['text'] . '</textarea>
                        <div>' . $errorTextMessage . '</div>
                    </div>
                    <div class="field">
                    <label for="rights">Genre:</label>
                        <select class="" name="editGenre">
                            <optgroug name="editGenre">
                                <option value="" disabled selected>' . $result['genre'] . '</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                                <option value="F">F</option>
                                <option value="G">G</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="field">
                        <label for="soundcloudLink">Soundcloud Link:</label>
                        <input type="url" name="soundcloudLink" id="soundcloudLink" placeholder="Soundcloud Link" maxlength="255" value="' . $result['soundcloud'] . '">
                        <div>' . $errorSoundcloudLink . '</div>
                    </div>
                    <div class="field">
                        <label for="youtubeLink">YouTube Link:</label>
                        <input type="url" name="youtubeLink" id="youtubeLink" placeholder="YouTube Link" maxlength="255" value="' . $result['youtube'] . '">
                        <div>' . $errorYouTubeLink . '</div>
                    </div>
                    <div class="field">
                    <label for="editUnlocked">Freischalten:</label>
                        <select class="" name="editUnlocked">
                            <optgroug name="editunlocked">
                                <option value="' . $result['unlocked'] . '" disabled selected>' . $result['unlocked'] . '</option>
                                <option value="Yes">Online</option>
                                <option value="No">Offline</option>
                            </optgroup>
                        </select>
                    </div>

                    <input class="btn btnSecondary" type="submit" name="edit" value="Aktualisieren">
                </form></div><br>';
            } else {
                global $errorUpdate, $errorTitle, $errorDescription, $errorTextMessage, $errorSoundcloudLink, $errorYouTubeLink;
                echo '<div class="formAdminUpdate"><form method="post" enctype="multipart/form-data">
                <input type="hidden" name="userId" value="' . $result['id'] . '">
                <input type="hidden" name="tableName" value="nlp">
                <input type="hidden" name="valueNlpImage" value="' . $profilPic['src'] . '">
                <input type="hidden" name="valueGenre" value="' . $result['genre'] . '">
                <input type="hidden" name="unlocked" value="' . $result['unlocked'] . '">
                <input type="hidden" name="xsrfToken" value="' . $_POST["xsrfToken"] . '">
                    <div>
                        <div>' . $errorUpdate . '</div>
                        <h1>Editieren</h1>
                    </div>
                    <div class="field">
                        <label for="title">Titel:</label>
                        <input type="text" name="title" id="title" placeholder="Titel" autofocus maxlength="75" value="' . $result['title'] . '">
                        <div>' . $errorTitle . '</div>
                    </div>
                    <div class="field">
                        <label for="description">Beschreibung:</label>
                        <input name="description" id="description" placeholder="Beschreibung" maxlength="75" value="' . $result['description'] . '">
                        <div>' . $errorDescription . '</div>
                    </div>
                    <div class="field">
                        <label for="text">Text:</label>
                        <textarea name="text" id="text" placeholder="Text" value="">' . $result['text'] . '</textarea>
                        <div>' . $errorTextMessage . '</div>
                    </div>
                    <div class="field">
                    <label for="rights">Genre:</label>
                        <select class="" name="editGenre">
                            <optgroug name="editGenre">
                                <option value="" disabled selected>' . $result['genre'] . '</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                                <option value="F">F</option>
                                <option value="G">G</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="field">
                        <label for="soundcloudLink">Soundcloud Link:</label>
                        <input type="url" name="soundcloudLink" id="soundcloudLink" placeholder="Soundcloud Link" maxlength="255" value="' . $result['soundcloud'] . '">
                        <div>' . $errorSoundcloudLink . '</div>
                    </div>
                    <div class="field">
                        <label for="youtubeLink">YouTube Link:</label>
                        <input type="url" name="youtubeLink" id="youtubeLink" placeholder="YouTube Link" maxlength="255" value="' . $result['youtube'] . '">
                        <div>' . $errorYouTubeLink . '</div>
                    </div>
                    <input class="btn btnSecondary" type="submit" name="edit" value="Aktualisieren">
                </form></div><br>';
            }
        }

    }
}
// Funktion, für den Show Button, es werden alle Daten gesondert von der Tabelle angezeigt
function showTableRow($tableName)
{

    if ($tableName == "userdata") {
        $result = database::getConnections()->view('userdata INNER JOIN userinfo ON userdata.id=userinfo.userid', 'userdata.id=:userid', [':userid' => $_POST["userId"]]);
        $image = database::getConnections()->viewByUSERID("images", $_POST["userId"]);

        // Wenn das Bild leer ist, dann verwende das standard Default Bild
        if (empty($image["src"])) {
            $image["src"] = "public/image/DefaultProfilePic.png";
        }

        // Wenn der Name des Bildes leer ist, dann verwende den eingegeben
        if (empty($profilPic["name"])) {
            $profilPic["name"] = "Default Profile Bild";
        }

        echo '<div class="textAlignCenter">
            <div class="field">
                <h2>Bild:</h2>
                <img src="' . ROOT_URL . $image["src"] . '" oncontextmenu="return false;" style="width:150px;">
            </div>
            <div class="field">
                <h2>Vorname:</h2>
                <input type="text" name="firstname" id="firstname" disabled title="Vorname" value="' . $result["firstname"] . '">
            </div>
            <div class="field">
                <h2>Nachname:</h2>
                <input type="text" name="lastname" id="lastname" disabled title="Nachname" value="' . $result["lastname"] . '">
            </div>
            <div class="field">
                <h2>Email Adresse:</h2>
                <input type="text" name="email" id="email" disabled title="Email Adresse" value="' . $result["email"] . '">
            </div>
            <div class="field">
                <h2>Geburtstag:</h2>
                <input type="text" name="birthday" id="birthday" disabled title="Geburtstag" value="' . $result["birthday"] . '">
            </div>
            <div class="field">
                <h2>Land:</h2>
                <input type="text" name="country" id="country" disabled title="Land" value="' . $result["country"] . '">
            </div>
            <div class="field">
                <h2>Registriert am:</h2>
                <input type="text" name="register" id="register" disabled title="Registriert" value="' . $result["registered"] . '">
            </div>
            <div class="field">
                <h2>Letzter Login am:</h2>
                <input type="text" name="login" id="login" disabled title="Eingeloggt" value="' . $result["lastlogin"] . '">
            </div>
            <div class="field">
                <h2>Status (1 = Gesperrt):</h2>
                <input type="text" name="status" id="status" disabled title="Status" value="' . $result["status"] . '">
            </div>
            <div class="field">
                <h2>Aktivierter Account (1 = Aktiviert):</h2>
                <input type="text" name="active" id="active" disabled title="Aktiviert" value="' . $result["active"] . '">
            </div>
            <div class="field">
                <h2>Rechte:</h2>
                <input type="text" name="rights" id="rights" disabled title="Rechte" value="' . $result["rights"] . '">
            </div>
            </div><br>';
    } else {
        $result = database::getConnections()->viewByID("nlp", $_POST["userId"]);
        $image = database::getConnections()->view("images", "nlpid=:nlpid", ["nlpid" => $_POST['userId']]);

        // Wenn das Bild leer ist, dann verwende das standard Default Bild
        if (empty($image["src"])) {
            $image["src"] = "public/image/DefaultNlpPic.png";
        }

        // Wenn der Name des Bildes leer ist, dann verwende den eingegeben
        if (empty($profilPic["name"])) {
            $profilPic["name"] = "Default NLP Bild";
        }

        echo '<div class="textAlignCenter">
            <div class="field">
                <h2>Bild:</h2>
                <img src="' . ROOT_URL . $image["src"] . '" oncontextmenu="return false;" style="width:150px;">
            </div>
            <div class="field">
                <h2>Autor:</h2>
                <input type="text" name="author" id="author" disabled title="Autor" value="' . $result["author"] . '">
            </div>
            <div class="field">
                <h2>Titel:</h2>
                <input type="text" name="title" id="title" disabled title="Titel" value="' . $result["title"] . '">
            </div>
            <div class="field">
                <h2>Beschreibung:</h2>
                <input type="text" name="description" id="description" disabled title="Beschreibung" value="' . $result["description"] . '">
            </div>
            <div class="field">
                <h2>Text:</h2>
                <textarea rows="8" cols="60" disabled>' . $result["text"] . '</textarea>
            </div>
            <div class="field">
                <h2>Genre:</h2>
                <input type="text" name="genre" id="genre" disabled title="Genre" value="' . $result["genre"] . '">
            </div>
            <div class="field">
                <h2>Soundcloud Link:</h2>
                <input type="text" name="soundcloud" id="soundcloud" disabled title="Soundcloud Link" value="' . $result["soundcloud"] . '">
            </div>
            <div class="field">
                <h2>Youtube Link:</h2>
                <input type="text" name="youtube" id="youtube" disabled title="Youtube Link" value="' . $result["youtube"] . '">
            </div>
            <div class="field">
                <h2>Hochgeladen am:</h2>
                <input type="text" name="uploaded" id="uploaded" disabled title="Hochgeladen am" value="' . $result["uploaded"] . '">
            </div>
            <div class="field">
                <h2>Freigeschalten (1 = Online):</h2>
                <input type="text" name="unlocked" id="unlocked" disabled title="Freigeschalten" value="' . $result["unlocked"] . '">
            </div>
            </div><br>';
    }
}
// Funktion, für den Delete Button. Mit dieser Funktion wird man erst einmal gefragt ob es wirklich löschen möchte
function deleteTableRow()
{
    echo '<form class="textAlignCenter" method="post" enctype="multipart/form-data">
    <h1> Willst du wirklich diese Reihe löschen? Einmal gelöscht, kann man die Daten nicht mehr retten! </h1>
    <input type="hidden" name="tableName" value="' . $_POST["tableName"] . '">
    <input type="hidden" name="userId" value="' . $_POST["userId"] . '">
    <input class="btn btnSecondary" type="submit" name="confirmDelete" value="Yes">
    <input class="btn btnSecondary" type="submit" name="confirmDelete" value="No"><br></form>';
}
// Funktion, damit man erstmal gefragt wird ob man das Passwort des Users ändern möchte
function confirmRandomPassword()
{
    $result = database::getConnections()->viewByID($_POST["tableName"], $_POST["userId"]);

    echo '<form class="textAlignCenter" method="post" enctype="multipart/form-data">
    <h1> Willst du wirklich das Passwort des Users ändern? Man kann dies nicht mehr rückgängig machen! </h1>
    <input type="hidden" name="tableName" value="' . $_POST["tableName"] . '">
    <input type="hidden" name="userId" value="' . $_POST["userId"] . '">
    <input type="hidden" name="userEmail" value="' . $result["email"] . '">
    <input class="btn btnSecondary" type="submit" name="confirmRandomPassword" value="Yes">
    <input class="btn btnSecondary" type="submit" name="confirmRandomPassword" value="No"><br></form>';
}
// Funktion, damit man dem User ein neues Passwird generiert und es ihm per Mail zuschickt
function userRandomPassword()
{

    global $errorSearch;

    $length = 20;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-._#!?%';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    $password_hash = password_hash($randomString, PASSWORD_DEFAULT);

    if (database::getConnections()->updateAdminRandomPassword("userdata", $_POST["userId"], $password_hash) == true) {
        $errorSearch = '<p class="success">Erfolgreich das Passwort geändert. Dem User wurde ein Random Passwort per E-Mail zugeschickt!</p>';

        // ACHTUNG // ACHTUNG // ACHTUNG // ACHTUNG // ACHTUNG // ACHTUNG // ACHTUNG // ACHTUNG
        // SPÄTER ENTFERNEN NUR ZUM AUSPROBIEREN UND SCHAUEN, SOLANGE AUCH DIE MAIL NICHT AUFTAUCHT!
        echo 'Das Neue Passwort von dem User lautet (Ohne Leerzeichen und "") = "' . $randomString . '" !';
    } else {
        $errorSearch = '<p class="error">Es ist ein Fehler aufgetreten und das Passwort des Users konnte nicht abgeändert werden. Bitte wende dich an den Besitzer und Backend Developer!</p>';
    }

    $newMail = new mail();
    $newMail->randomPasswordMail($_POST["userEmail"], $randomString);
}
// Funktion, damit man ein neues Formular anzeigen kann für neue NLP Übungen oder neue User Anlegen
function newTableRow($tableName)
{

    if ($tableName == "userdata") {
        $errorRegister = $errorFirstName = $errorLastName = $errorEmail = $errorPassword = $errorPasswordRepeat = $errorAGBCheckbox = "";

        $tokenClass = new token();
        $xsrfType = "AdminNewUserForm";
        $xsrfExpires = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +10 minutes"));

        $xsrfToken = $tokenClass->generateXSRFToken();
        database::getConnections()->tokenInsert($xsrfToken, $xsrfType, $xsrfExpires, $xsrfId = null);

        echo '<div class="formAdminRegister"><form method="post" enctype="multipart/form-data">
        <input type="hidden" name="tableName" value="userdata">
        <input type="hidden" name="xsrfToken" value="' . $xsrfToken . '">
        <div>
            <div>' . $errorRegister . '</div>
            <h1>Registrieren</h1>
        </div>
            <div class="field">
                <label for="firstnameRegisterAdmin">Vorname:</label>
                <input type="text" name="firstnameRegisterAdmin" id="firstnameRegisterAdmin" placeholder="Vorname" title="Beispiel = Manfred Müller" autofocus maxlength="75" value="">
                <div>' . $errorFirstName . '</div>
            </div>
            <div class="field">
                <label for="lastnameRegisterAdmin">Nachname:</label>
                <input type="text" name="lastnameRegisterAdmin" id="lastnameRegisterAdmin" placeholder="Nachname" title="Beispiel = Trautmann-Neuhagen" maxlength="75" value="">
                <div>' . $errorLastName . '</div>
            </div>
            <div class="field">
                <label for="emailRegisterAdmin">Email Adresse:</label>
                <input type="email" name="emailRegisterAdmin" id="emailRegisterAdmin" placeholder="E-Mail Adresse" title="Beispiel = max-Mustermann123@gmail.com" maxlength="255" value="">
                <div>' . $errorEmail . '</div>
            </div>
            <div class="field">
                <label for="passwordRegisterAdmin">Passwort:</label>
                <input type="password" name="passwordRegisterAdmin" id="passwordRegisterAdmin" placeholder="Passwort" title="Das Passwort muss einen klein und groß Buchstaben, eine Zahl und ein Sonderzeichen enthalten" autocomplete="off" maxlength="100">
                <div>' . $errorPassword . '</div>
            </div>
            <div class="field">
                <label for="passwordRepeatRegisterAdmin">Passwort wiederholen:</label>
                <input type="password" name="passwordRepeatRegisterAdmin" id="passwordRepeatRegisterAdmin" placeholder="Passwort wiederholen!" title="Das Passwort muss wiederholt werden!" autocomplete="off" maxlength="100">
                <div>' . $errorPasswordRepeat . '</div>
            </div>
            <div class="field">
                <label for="agbCheckboxRegisterAdmin">
                    <input type="checkbox" name="agbCheckboxRegisterAdmin" id="agbCheckboxRegisterAdmin"><a class="agbDatenschutzLink" href="agb.php">AGB</a> & <a class="agbDatenschutzLink" href="datenschutz.php">DATENSCHUTZBESTIMMUNGEN</a> Akzeptieren
                </label>
                <div>' . $errorAGBCheckbox . '</div>
            </div>
            <br>
            <input class="btn btnSecondary" type="submit" name="edit" value="Registrieren">
        </form></div><br>';
    } else {

        $errorRegister = $errorSections = $errorTitle = $errorDescription = $errorTextMessage = $errorSoundcloudLink = $errorYouTubeLink = $errorAGBCheckbox = $errorImage = "";

        $tokenClass = new token();
        $xsrfType = "AdminNewNLPForm";
        $xsrfExpires = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +10 minutes"));

        $xsrfToken = $tokenClass->generateXSRFToken();
        database::getConnections()->tokenInsert($xsrfToken, $xsrfType, $xsrfExpires, $xsrfId = null);

        echo '<div class="formAdminRegister"><form method="post" enctype="multipart/form-data">
            <input type="hidden" name="tableName" value="nlp">
            <input type="hidden" name="xsrfToken" value="' . $xsrfToken . '">
                <div>
                    <div>' . $errorRegister . '</div>
                    <h1>Importieren</h1>
                </div>
                <div class="field">
                    <label for="nlpImage">Übungsbild:</label>
                    <input type="file" required accept="image/*" name="appImage" id="nlpImage">
                    <div>' . $errorImage . '</div>
                </div>
                <div class="field">
                    <label for="title">Titel:</label>
                    <input type="text" name="title" id="title" placeholder="Titel" autofocus maxlength="45" value="">
                    <div>' . $errorTitle . '</div>
                </div>
                <div class="field">
                    <label for="description">Beschreibung:</label>
                    <input type="text" name="description" id="description" placeholder="Beschreibung" maxlength="200" value="">
                    <div>' . $errorDescription . '</div>
                </div>
                <div class="field">
                    <label for="text">Text:</label>
                    <textarea name="text" id="text" placeholder="Text" rows="4" cols="60"></textarea>
                    <div>' . $errorTextMessage . '</div>
                </div>
                <div class="field">
                    <label for="rights">Genre:</label>
                    <select class="" name="genre">
				        <optgroug name="genre">
                            <option value="" disabled selected>Wähle aus!</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            <option value="F">F</option>
                            <option value="G">G</option>
				        </optgroup>
                    </select>
                    <div>' . $errorSections . '</div>
                </div>
                <div class="field">
                    <label for="soundcloudLink">Soundcloud Link/SRC:</label>
                    <input type="url" name="soundcloudLink" id="soundcloudLink" placeholder="Soundcloud Link" maxlength="255" value="">
                    <div>' . $errorSoundcloudLink . '</div>
                </div>
                <div class="field">
                    <label for="youtubeLink">YouTube Link/SRC:</label>
                    <input type="url" name="youtubeLink" id="youtubeLink" placeholder="YouTube Link" maxlength="255" value="">
                    <div>' . $errorYouTubeLink . '</div>
                </div>
                <div class="field">
                    <label for="agbCheckbox">
                        <input type="checkbox" name="agbCheckbox" id="agbCheckbox"><a class="agbDatenschutzLink" href="agb.php">AGB</a> & <a class="agbDatenschutzLink" href="datenschutz.php">DATENSCHUTZBESTIMMUNGEN</a> Akzeptieren
                    </label>
                <div>' . $errorAGBCheckbox . '</div>
            </div>
                <input class="btn btnSecondary" type="submit" name="edit" value="Importieren">
            </form></div><br>';
    }

}
// Funktion, damit die Daten von dem neuen Formular in die Database gespeichert werden
function insertTableRow($tableName)
{

    if ($tableName == "userdata") {
        global $errorRegister, $errorFirstName, $errorLastName, $errorEmail, $errorPassword, $errorPassword, $errorPasswordRepeat, $errorRegister, $errorAGBCheckbox;

        $errorRegister = $errorFirstName = $errorLastName = $errorEmail = $errorPassword = $errorPasswordRepeat = $errorAGBCheckbox = $firstnameInput = $lastnameInput = $emailInput = $passwordInput = $passwordRepeatInput = $checkboxInput = "";
        if (!isset($_POST['agbCheckboxRegisterAdmin'])) {
            $_POST['agbCheckboxRegisterAdmin'] = "";
        }

        $tokenClass = new token();
        $xsrfType = "AdminNewUserForm";

        $firstnameInput = sanitize_input(ucfirst($_POST['firstnameRegisterAdmin']));
        $lastnameInput = sanitize_input(ucfirst($_POST['lastnameRegisterAdmin']));
        $emailInput = sanitize_input($_POST['emailRegisterAdmin']);
        $passwordInput = sanitize_input($_POST['passwordRegisterAdmin']);
        $passwordRepeatInput = sanitize_input($_POST['passwordRepeatRegisterAdmin']);
        $checkboxInput = $_POST['agbCheckboxRegisterAdmin'];

        $validation = new validation();
        $registrationValidation = $validation->validRegister($firstnameInput, $lastnameInput, $emailInput, $passwordInput, $passwordRepeatInput, $checkboxInput);

        if ($registrationValidation == true) {
            $data = database::getConnections()->view("userdata", "email=:email", [':email' => $emailInput]);
            if ($data == false) {
                if (($tokenData = $tokenClass->checkXSRFToken($_POST["xsrfToken"])) == true) {
                    $password_hash = password_hash($passwordInput, PASSWORD_DEFAULT);
                    $ipAddress = get_client_ip();

                    if (database::getConnections()->insertRegisterUserData("userdata", $firstnameInput, $lastnameInput, $emailInput, $password_hash) == true) {
                        $data = database::getConnections()->view("userdata", "email=:email", [':email' => $emailInput]);
                        if (database::getConnections()->insertRegisterUserInfo("userinfo", $data["id"], $ipAddress) == true) {
                            echo '<p class="success">Sie wurden erfolgreich registriert!</p>';
                        }
                    } else {
                        echo $errorSearch = '<p class="error">Beim Anlegen Ihres Accounts ist ein fehler aufgetreten! Versuchen Sie es erneut!</p>';
                    }

                    $tokenClass->deleteXSRFToken($tokenData["id"], $xsrfType);

                    // Es wird ein Token erstellt zum Aktivieren des Accounts, dieser wird auch hochgeladen in die "token" Tabelle
                    $xsrfTypeRegisterActivate = "RegisterAccountActivate";
                    $xsrfExpiresRegisterActivate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +1440 minutes")); // 24 Stunden (1 Tage)
                    $xsrfTokenRegisterActivate = $tokenClass->generateXSRFToken();
                    database::getConnections()->tokenInsert($xsrfTokenRegisterActivate, $xsrfTypeRegisterActivate, $xsrfExpiresRegisterActivate, $data["id"]);

                    // Schreibe dem User eine E-Mail mit allen Registrierungsdaten und dem Aktivierungslink
                    $newMail = new mail();
                    $newMail->registerAccountDataMail($emailInput, $firstnameInput, $lastnameInput, $ipAddress, $xsrfTokenRegisterActivate, $data["id"]);

                    // ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //// ACHTUNG! //
                    // Diese zwei Links hier unten drunter sind nur dafür da, für die Entwicklung, also wenn man keine E-Mail enthalten kann
                    global $errorSearch;
                    $errorSearch = '<a href="http://localhost:8888/Boost-Your-Life/resources/login.php?token=' . $xsrfTokenRegisterActivate . '&id=' . $data["id"] . '">Klicken Sie hier um Ihren Account zu aktivieren! (Dieser Link besteht nur, weil man über MAMP keine E-Mails verschicken kann!)</a>';

                    // Free Googie Host Server
                    // $errorSearch = '<a href="http://boost-your-life.thats.im/resources/login.php?token=' . $xsrfTokenRegisterActivate . '&id=' . $data["id"] . '">Klicken Sie hier um Ihren Account zu aktivieren! (Dieser Link besteht nur, weil man bei dem Kostenlosen Server sehr oft keine E-Mails bekommt und es auch ein E-Mail Limit gibt!)</a>';
                } else {
                    global $errorSearch;
                    echo $errorSearch = '<p class="error">Bitte versuchen Sie es nach einem Refresh erneut! Wenn nach dem Refresh das Problem immer noch besteht, so wenden Sie sich bitte an den Support ===> support@boost-your-life.de</p>';
                }
            } else {
                global $errorSearch;
                echo $errorSearch = '<p class="error">Diese E-Mail Adresse ist schon vergeben, bitte eine andere eingeben!</p>';
            }
        } elseif ($registrationValidation == false || $data == true || $tokenData == false) {
            global $errorEmail, $errorRegister;
            echo $form = '<div class="formAdminRegister"><form method="post" enctype="multipart/form-data">
            <input type="hidden" name="tableName" value="user">
            <input type="hidden" name="xsrfToken" value="' . $_POST["xsrfToken"] . '">
            <div>
                <div>' . $errorRegister . '</div>
                <h1>Registrieren</h1>
            </div>
                <div class="field">
                    <label for="firstnameRegisterAdmin">Vorname:</label>
                    <input type="text" name="firstnameRegisterAdmin" id="firstnameRegisterAdmin" placeholder="Vorname" title="Beispiel = Manfred Müller" autofocus maxlength="75" value="' . $firstnameInput . '">
                    <div>' . $errorFirstName . '</div>
                </div>
                <div class="field">
                    <label for="lastnameRegisterAdmin">Nachname:</label>
                    <input type="text" name="lastnameRegisterAdmin" id="lastnameRegisterAdmin" placeholder="Nachname" title="Beispiel = Trautmann-Neuhagen" maxlength="75" value="' . $lastnameInput . '">
                    <div>' . $errorLastName . '</div>
                </div>
                <div class="field">
                    <label for="emailRegisterAdmin">Email Adresse:</label>
                    <input type="email" name="emailRegisterAdmin" id="emailRegisterAdmin" placeholder="E-Mail Adresse" title="Beispiel = max-Mustermann123@gmail.com" maxlength="255" value="' . $emailInput . '">
                    <div>' . $errorEmail . '</div>
                </div>
                <div class="field">
                    <label for="passwordRegisterAdmin">Passwort:</label>
                    <input type="password" name="passwordRegisterAdmin" id="passwordRegisterAdmin" placeholder="Passwort" title="Das Passwort muss einen klein und groß Buchstaben, eine Zahl und ein Sonderzeichen enthalten" autocomplete="off" maxlength="100">
                    <div>' . $errorPassword . '</div>
                </div>
                <div class="field">
                    <label for="passwordRepeatRegisterAdmin">Passwort wiederholen:</label>
                    <input type="password" name="passwordRepeatRegisterAdmin" id="passwordRepeatRegisterAdmin" placeholder="Passwort wiederholen!" title="Das Passwort muss wiederholt werden!" autocomplete="off" maxlength="100">
                    <div>' . $errorPasswordRepeat . '</div>
                </div>
                <div class="field">
                    <label for="agbCheckboxRegisterAdmin">
                        <input type="checkbox" name="agbCheckboxRegisterAdmin" id="agbCheckboxRegisterAdmin"><a class="agbDatenschutzLink" href="agb.php">AGB</a> & <a class="agbDatenschutzLink" href="datenschutz.php">DATENSCHUTZBESTIMMUNGEN</a> Akzeptieren
                    </label>
                    <div>' . $errorAGBCheckbox . '</div>
                </div>
                <br>
                <input class="btn btnSecondary" type="submit" name="edit" value="Registrieren">
            </form></div><br>';
        }
    } else {

        $errorSections = $errorRegister = $errorTitle = $errorDescription = $errorTextMessage = $errorSoundcloudLink = $errorYouTubeLink = $errorAGBCheckbox = "";
        global $newUser, $userid, $errorTextMessage, $errorRegister, $errorTitle, $errorDescription, $errorSoundcloudLink, $errorYouTubeLink, $errorAGBCheckbox, $errorSections;

        $tokenClass = new token();
        $xsrfType = "AdminNewNLPForm";

        $author = $newUser->userFirstName . " " . $newUser->userLastName;
        $titleInput = sanitize_input(ucfirst($_POST['title']));
        $descriptionInput = sanitize_input(ucfirst($_POST['description']));
        $textInput = sanitize_input($_POST['text']);
        $soundcloudInput = sanitize_input($_POST['soundcloudLink']);
        $youtubeInput = sanitize_input($_POST['youtubeLink']);
        $checkboxInput = $_POST['agbCheckbox'];
        $genreInput = $_POST['genre'];

        $validation = new validation();
        $registrationValidation = $validation->validRegisterNLP($titleInput, $descriptionInput, $textInput, $genreInput, $soundcloudInput, $youtubeInput, $checkboxInput);

        if ($registrationValidation == true) {
            $data = database::getConnections()->view("nlp", "userid");
            if ($data == true) {
                if (($tokenData = $tokenClass->checkXSRFToken($_POST["xsrfToken"])) == true) {

                    require_once '../functions/imageUpload.php';

                    // Es wird geschaut ob ein Bild hochgeladen wurde
                    if (!$_FILES['appImage']['size'] == 0) {
                        $unlink = "";
                        $bildFunktion = imageUploads("insert", "Nlp", $userid, $id, $unlink);

                        // Wenn das Bild erfolgreich hochgeladen wurde, dann lade die NLP Übung mit den Inhalten hoch
                        if ($bildFunktion == false) {
                            if (database::getConnections()->insertDataNLP("nlp", $userid, $author, $genreInput, $titleInput, $descriptionInput, $textInput, $soundcloudInput, $youtubeInput) == true) {
                                global $errorSearch;
                                $errorSearch = '<p class="success">Die NLP Übung wurde erfolgreich angelegt!</p>';

                                $nlpData = database::getConnections()->view("nlp", "userid=:userid ORDER BY uploaded DESC LIMIT 1", [':userid' => $userid]);
                                $imageData = database::getConnections()->view("images", "userid=:userid ORDER BY uploaded DESC LIMIT 1", [':userid' => $userid]);

                                database::getConnections()->updateNlpImageID($nlpData["id"], $imageData["id"]);

                                // $nlpData = database::getConnections()->view("nlp", "userid=:userid ORDER BY uploaded DESC LIMIT 1", [':userid' => $userid]);

                                // database::getConnections()->updateNlpImageID($nlpData["id"], $userid);
                            } else {
                                global $errorSearch;
                                $errorSearch = '<p class="error">Beim Anlegen der NLP Übung ist ein fehler aufgetreten!</p>';
                            }
                            $tokenClass->deleteXSRFToken($tokenData["id"], $xsrfType);
                        }

                    } else {
                        global $errorImage;
                        $errorImage = '<p class="errorMessages">Bitte wählen Sie ein Bild aus!</p>';
                    }

                } else {
                    global $errorRegister;
                    $errorRegister = '<p class="errorMessages">Bitte versuchen Sie es nach einem Refresh erneut! Wenn nach dem Refresh das Problem immer noch besteht, so wenden Sie sich bitte an den Support = support@web.de</p>';
                }
            }
        } elseif ($registrationValidation == false || $data == false || $tokenData == false || $bildFunktion == true || $_FILES['appImage']['size'] == 0) {
            global $errorRegister, $errorImage;
            echo '<div class="formAdminRegister"><form method="post" enctype="multipart/form-data">
            <input type="hidden" name="tableName" value="nlp">
            <input type="hidden" name="xsrfToken" value="' . $_POST["xsrfToken"] . '">
                <div>
                    <div>' . $errorRegister . '</div>
                    <h1>Importieren</h1>
                </div>
                <div class="field">
                    <label for="nlpImage">Übungsbild:</label>
                    <input type="file" required accept="image/*" name="appImage" id="nlpImage">
                    <div>' . $errorImage . '</div>
                </div>
                <div class="field">
                    <label for="title">Titel:</label>
                    <input type="text" name="title" id="title" placeholder="Titel" autofocus maxlength="45" value="' . $titleInput . '">
                    <div>' . $errorTitle . '</div>
                </div>
                <div class="field">
                    <label for="description">Beschreibung:</label>
                    <input type="text" name="description" id="description" placeholder="Beschreibung" maxlength="200" value="' . $descriptionInput . '">
                    <div>' . $errorDescription . '</div>
                </div>
                <div class="field">
                    <label for="text">Text:</label>
                    <textarea name="text" id="text" placeholder="Text" rows="4" cols="60">' . $textInput . '</textarea>
                    <div>' . $errorTextMessage . '</div>
                </div>
                <div class="field">
                    <label for="rights">Genre:</label>
                    <select class="" name="genre" id="genre">
				        <optgroug name="genre" id="genre">
                            <option value="' . $genreInput . '" disabled selected>Wähle aus!</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            <option value="F">F</option>
                            <option value="G">G</option>
				        </optgroup>
                    </select>
                    <div>' . $errorSections . '</div>
                </div>
                <div class="field">
                    <label for="soundcloudLink">Soundcloud Link/SRC:</label>
                    <input type="url" name="soundcloudLink" id="soundcloudLink" placeholder="Soundcloud Link" maxlength="255" value="' . $soundcloudInput . '">
                    <div>' . $errorSoundcloudLink . '</div>
                </div>
                <div class="field">
                    <label for="youtubeLink">YouTube Link/SRC:</label>
                    <input type="url" name="youtubeLink" id="youtubeLink" placeholder="YouTube Link" maxlength="255" value="' . $youtubeInput . '">
                    <div>' . $errorYouTubeLink . '</div>
                </div>
                <div class="field">
                    <label for="agbCheckbox">
                        <input type="checkbox" name="agbCheckbox" id="agbCheckbox"><a class="agbDatenschutzLink" href="agb.php">AGB</a> & <a class="agbDatenschutzLink" href="datenschutz.php">DATENSCHUTZBESTIMMUNGEN</a> Akzeptieren
                    </label>
                <div>' . $errorAGBCheckbox . '</div>
            </div>
                <input class="btn btnSecondary" type="submit" name="edit" value="Importieren">
            </form></div><br>';
        }

    }

}
?>
    </main>
</body>
</html>