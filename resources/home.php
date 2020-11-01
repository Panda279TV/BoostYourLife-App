<?php
// Die Config Datei wird required, also reingeladen
require_once '../core/init.php';
// Speichere die Userdaten Klasse in die Variable
$newUser = new user();

// Schaut ob der User eingeloggt ist oder nicht, da dass ein Bereich nach dem Login ist, ist es wichtig ausgeloggte User nicht weiter kommen zu lassen
if ($newUser->is_loggedIn() == false) {
    $newUser->logout();
    redirect::to('404');
}

// Der Header wird eingebunden
include ROOT_DIR . 'includes/assets/header.php';
?>
<main class="homeMain">
    <?php

$userid = $newUser->userId;
?>
            <div class="circleHomeWrapper"><div class="circleHome"></div></div>
            <div class="nlpAllSlider">
            <?php

$searchdata = database::getConnections()->viewNlpImages();

// Wenn das Bild leer ist, dann verwende das standard Default Bild
if (empty($entry["src"])) {
    $entry["src"] = "public/image/DefaultNlpPic.png";
}

// Wenn das Bild leer ist, dann verwende das standard Default Bild
if (empty($entry["name"])) {
    $entry["name"] = "Default NLP Bild";
}

$slider = '<div class="swiper-home-newNLP">
                <h1>Neuste Übungen</h1>
                <div class="swiper-wrapper">';

foreach ($searchdata as $entry) {
    $slider .= '<div class="swiper-slide">
                    <img src="' . ROOT_URL . $entry['src'] . '" alt="' . $entry['name'] . '" oncontextmenu="return false;">
                    <h2>' . $entry['title'] . '</h2>
                    <p>' . $entry['author'] . '</p>
                </div>';
}

$slider .= '</div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div><br>';

echo $slider;
?>
        </div>
        <!-- Nächster Slider -->
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
</main>
<?php
// Der Footer wird eingebunden
include ROOT_DIR . 'includes/assets/footer.php';
?>