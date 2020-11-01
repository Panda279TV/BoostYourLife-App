<?php
// Die Config Datei wird required, also reingeladen
require_once '../core/init.php';
// Der Header wird eingebunden
include ROOT_DIR . 'includes/assets/header.php';
// Speichere die Userdaten Klasse in die Variable
$newUser = new user();

// Schaut ob der User eingeloggt ist oder nicht, da dass ein Bereich nach dem Login ist, ist es wichtig ausgeloggte User nicht weiter kommen zu lassen
if ($newUser->is_loggedIn() == false) {
    $newUser->logout();
    redirect::to('404');
}

// Wenn die $_POSTS nicht existieren, dann mache sie leer
if (!isset($_POST['search'])) {
    $_POST['search'] = "";
}

if (!isset($_POST['submit'])) {
    $_POST['submit'] = "";
}

// Speichere die Variablen als leer
$textSucheInput = "";
// Speichere den Input in einer Variable
$input = $_POST["search"];
// Wenn der Input nicht leer ist, dann zeige den Text an
if (!empty($input)) {
    $textSucheInput = 'Du hast nach " ' . $input . ' " gesucht!';
}
?>
    <main>
        <div class="searchSiteWrapper">
            <form method="post">
                <br>
                <h1>Suche</h1>
                <h4>Wenn du das Suchfeld leer lässt, suchst du nach allem, fülle es aus um spezifischer zu suchen!</h4>
                <h4><?=$textSucheInput;?></h4>
                <input class="searchNLPInput" type="text" name="search" id="searchNLPInput" placeholder="Nach Übungen Suchen!" value="">
                <br>
                <input class="btn btnSecondary" type="submit" name="submit" value="Suchen">
            </form>
            <div style="width: 100%;"><hr></div>
            <br>
        <?php
    // Wenn Suchen geklickt wurde, dann zeig bitte alle Übungen an
    if ($_POST["submit"] == "Suchen") {
        //$searchdata = database::getConnections()->view("nlp", "unlocked=true AND (title LIKE '%$input%' OR author LIKE '%$input%' OR description LIKE '%$input%' OR text LIKE '%$input%') ORDER BY uploaded DESC");

        $searchdata = database::getConnections()->viewNlpImagesInputSearch($input);

        // Wenn das Bild leer ist, dann verwende das standard Default Bild
        if (empty($entry["src"])) {
            $entry["src"] = "public/image/DefaultNlpPic.png";
        }

        // Wenn das Bild leer ist, dann verwende das standard Default Bild
        if (empty($entry["name"])) {
            $entry["name"] = "Default NLP Bild";
        }

        $searchOutput = '<div class="searchOutputWrapper">
                    <div class="searchOutput outputCard">';
        // Schleife, weil wir wollen ja alle Sachen ausgeben
        foreach ($searchdata as $entry) {
            $searchOutput .= '<div class="output">
                                <img src="' . ROOT_URL . $entry['src'] . '" alt="' . $entry['name'] . '" oncontextmenu="return false;">
                                <h1>' . $entry['title'] . '</h1>
                                <p>' . $entry['author'] . '</p>
                                <p>' . $entry['description'] . '</p>
                            </div>';
        }

        $searchOutput .= '</div>
                        </div>';

        echo $searchOutput;
    }
    ?>
        </div>
    </main>
</body>
</html>