<?php
// Die Config Datei wird required, also reingeladen
require_once 'core/init.php';

// Der Header wird eingebunden
include ROOT_DIR . 'includes/assets/header.php';

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
// elseif (empty($_COOKIE['Visited'])) {
//     setcookie("Visited", "1", time() + 3600 * 24 * 365);
// } elseif (!empty($_COOKIE['Visited'])) {
//     redirect::to('resources/login');
// }
?>
    <main>
    <a href="index.php"><img class="logo" src="public/image/logo.png" alt="Boost Your Life Logo"></a>
        <div class="sliderIndexWrapper">
            <div class="swiper-index-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <h1>Persönlichkeitsentwicklung einfach erleben</h1>
                        <p>Werde Dein bestes Selbst und noch Erfolgreicher!</p>
                        <img src="public/image/bild1.png" alt="Eine Frau liest ein Buch, sie sitzt auf gestapelten Büchern" oncontextmenu="return false;">
                    </div>
                    <div class="swiper-slide">
                        <h1>Individuelle Coaching Übungen auf Dich angepasst</h1>
                        <p>Praktische NLP Formate für verschiedene Anlässe</p>
                        <img src="public/image/bild2.png" alt="Ein Handy mit Komponenten" oncontextmenu="return false;">
                    </div>
                    <div class="swiper-slide">
                        <h1>Direkt loslegen: Die erste Übung</h1>
                        <p>Moment of Exscellence</p>
                        <iframe src="https://www.youtube-nocookie.com/embed/t8hEbffbiF4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    <div class="swiper-slide">
                        <h1>Worauf wartest Du noch?</h1>
                        <p>Erhalte jede Woche kostenlose weitere praktische Übungen & werde die Person die du immer Sein willst!</p>
                        <img src="public/image/bild3.png" alt="Vier Menschen, die verschiedende Sachen halten" oncontextmenu="return false;">
                    </div>
                    <div class="swiper-slide">
                        <h1>Melde dich sofort an oder Logge dich ein!</h1>
                        <div>
                            <a class="btn btnPrimary" href="resources/login.php">Einloggen</a>
                            <br>
                            <a class="btn btnPrimary" href="resources/register.php">Registrieren</a>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </main>
</body>
</html>
