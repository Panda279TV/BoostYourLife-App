<?php
// Die Config Datei wird required, also reingeladen
require_once '../../core/init.php';
// Der Header wird eingebunden
include ROOT_DIR . 'includes/assets/header.php';
?>
<main>
	<div class="errorSiteWrapper">
		<h1>ETWAS IST SCHIEF GELAUFEN!</h1>
		<h3>EIN BOOST TECHNIKER IST GERADE AUF DEM WEG UND WIRD SICH DAS GANZE ANSCHAUEN</h3>
		<h3>ES KÖNNTE SEIN, DASS DIESE SEITE NICHT EXISTIERT ODER IHNEN DIE BERECHTIGUNG DAFÜR FEHLT</h3>
		<h3>MELDEN SIE SICH AN ODER VERSUCHEN SIE ES NOCH EINMAL ERNEUT</h3>
		<h4><a href="<?=ROOT_URL;?>index.php">KLICKEN SIE HIER! ==> DIE STARTSEITE IHR FREUND UND HELFER!</a></h4>
		<br>
		<h2>IHR BOOST YOUR LIFE TEAM</h2>
	</div>
</main>
    <script>
        function addDot() {
            var x = $(window).scrollTop();
            var y = $(window).scrollLeft();
			var div = document.createElement("div");
			div.classList.add("dotBackground");
            document.body.appendChild(div);
            div.style.top = Math.random() * document.documentElement.clientHeight + "px";
            div.style.left = Math.random() * document.documentElement.clientWidth + "px";
            setTimeout(function() {
                div.parentNode.removeChild(div);
            }, 8000);
        }
        setInterval(addDot, 400);
    </script>
</body>
</html>