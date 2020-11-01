<?php
// Wenn die $_SESSION nicht existiert, dann mache sie leer
if (!isset($_SESSION["id"])) {
    $_SESSION["id"] = "";
}

// In der Datenbank wird nach dem User gesucht und nach den Rechten
$userRightsMenu = database::getConnections()->view("userinfo", "userid=:userid", [':userid' => $_SESSION["id"]]);
// If Else abfrage, da der User nicht den Admin Link sehen soll. Zum anderen soll aber auf der Startseite/Login/Registration das Impressum etc angezeigt werden und mehr nicht

// Schaut ob ein Profil Bild in der Datenbank vorhanden ist
$profilPic = database::getConnections()->view("images", "userid=:userid AND tablename=:tablename ORDER BY uploaded DESC LIMIT 1", [':userid' => $_SESSION["id"], ':tablename' => 'Profile']);

// Wenn das Bild leer ist, dann verwende das standard Default Bild
if (empty($profilPic["src"])) {
    $profilPic["src"] = "public/image/DefaultProfilePic.png";
}

// Wenn der Name des Bildes leer ist, dann verwende den eingegeben
if (empty($profilPic["name"])) {
    $profilPic["name"] = "Default Profile Bild";
}

// Schaut ob die Id vorhanden ist und dann gib das dort unten drunter aus, also wenn man nicht eingeloggt ist. Ist man aber eingeloggt wird geschaut welche Rechte der User hat und dann wird wenn man ein Normaler User ist auch kein Admin Bereich angezeigt
if (empty($_SESSION["id"])) {
    echo '<nav class="navHeader">
            <li class="navItem"><div class="hamburger"><span></span></div></span>
            <div class="lightbox hidden">
                <li class="navItem hamburgerDropdown hidden">
                    <div class="dropdownMenu">
                        <a class="dropdownItem itemLastHeaderExtraMarginTop" href="' . ROOT_URL . 'index.php">Startseite</a>
                        <a class="dropdownItem" href="' . ROOT_URL . 'resources/login.php">Login</a>
                        <a class="dropdownItem" href="' . ROOT_URL . 'resources/register.php">Register</a>
                        <a class="dropdownItem ItemLast itemLastHeaderExtraMarginTop" href="' . ROOT_URL . 'resources/impressum.php">Impressum</a>
                        <!-- <a class="dropdownItem ItemLast" href="' . ROOT_URL . 'resources/agb.php">AGB</a> -->
                        <a class="dropdownItem ItemLast" href="' . ROOT_URL . 'resources/datenschutz.php">Datenschutz</a>
                    </div>
                </li>
            </div>
        </nav>';
} elseif ($userRightsMenu["rights"] > 20) {
    echo '<nav class="navHeader">
            <li class="navItem"><div class="hamburger"><span></span></div></span>
            <div class="lightbox hidden">
                <li class="navItem hamburgerDropdown hidden">
                    <div class="dropdownMenu">
                        <a class="dropdownItem headerExtraMarginTop" href="' . ROOT_URL . 'resources/profile.php"><img src="' . ROOT_URL . $profilPic["src"] . '" alt="' . $profilPic["name"] . '" oncontextmenu="return false;"></a>
                        <a class="dropdownItem" href="' . ROOT_URL . 'resources/admin.php">Admin</a>
                        <a class="dropdownItem" href="' . ROOT_URL . 'resources/home.php">Startseite</a>
                        <a class="dropdownItem" href="' . ROOT_URL . 'resources/search.php">Suche</a>
                        <a class="dropdownItem" href="#">Vollversion</a>
                        <a class="dropdownItem" href="' . ROOT_URL . 'resources/logout.php">Logout</a>
                        <a class="dropdownItem ItemLast itemLastHeaderExtraMarginTop" href="' . ROOT_URL . 'resources/impressum.php">Impressum</a>
                        <!-- <a class="dropdownItem ItemLast" href="' . ROOT_URL . 'resources/agb.php">AGB</a> -->
                        <a class="dropdownItem ItemLast" href="' . ROOT_URL . 'resources/datenschutz.php">Datenschutz</a>
                    </div>
                </li>
            </div>
        </nav>';
} elseif ($userRightsMenu["rights"] < 21) {
    echo '<nav class="navHeader">
            <li class="navItem"><div class="hamburger"><span></span></div></span>
            <div class="lightbox hidden">
                <li class="navItem hamburgerDropdown hidden">
                    <div class="dropdownMenu">
                        <a class="dropdownItem headerExtraMarginTop" href="' . ROOT_URL . 'resources/profile.php"><img src="' . ROOT_URL . $profilPic["src"] . '" alt="' . $profilPic["name"] . '" oncontextmenu="return false;"></a>
                        <a class="dropdownItem" href="' . ROOT_URL . 'resources/home.php">Startseite</a>
                        <a class="dropdownItem" href="' . ROOT_URL . 'resources/search.php">Suche</a>
                        <a class="dropdownItem" href="#">Vollversion</a>
                        <a class="dropdownItem" href="' . ROOT_URL . 'resources/logout.php">Logout</a>
                        <a class="dropdownItem ItemLast itemLastHeaderExtraMarginTop" href="' . ROOT_URL . 'resources/impressum.php">Impressum</a>
                        <!-- <a class="dropdownItem ItemLast" href="' . ROOT_URL . 'resources/agb.php">AGB</a> -->
                        <a class="dropdownItem ItemLast" href="' . ROOT_URL . 'resources/datenschutz.php">Datenschutz</a>
                    </div>
                </li>
            </div>
        </nav>';
}
