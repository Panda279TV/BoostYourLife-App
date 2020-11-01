<?php
// Erstellt die Klasse mail
class mail
{
    // Macht Variablen Privat
    private $empfaenger,
    $betreff,
    $nachricht,
    $headers,
    $userId,
    $ipAdress,
    $emailInput,
    $firstNameInput,
    $lastNameInput,
    $tokenString,
    $birthdayInput,
    $countryInput;

    // Schickt eine HTML Mail raus, mit den mitgelieferten Daten
    public function registerAccountDataMail($emailInput, $firstNameInput, $lastNameInput, $ipAddress, $tokenString, $userId)
    {
        $empfaenger = $emailInput;
        $betreff = 'Boost-Your-Life --- Ihr Account wurde erfolgreich Registriert!';
        $nachricht = '
          <html>
          <head>
          <title>Ihre Daten von der Registrierung!</title>
          </head>
          <body>
          <main>
          <h1>Ihre Daten von der Registrierung:</h1>
          <p>------------------------------</p>
          <h3>Ihr Vorname:</h3>
          <p>' . $firstNameInput . '</p>
          <h3>Ihr Nachname:</h3>
          <p>' . $lastNameInput . '</p>
          <h3>Ihre E-Mail Adresse:</h3>
          <p>' . $emailInput . '</p>
          <h3>Ihr Passwort:</h3>
          <p>Ihr Passwort können nur Sie wissen!</p> <a target="_blank" href="http://boost-your-life.thats.im/resources/forgotpassword.php">Passwort Vergessen?</a>
          <h3>Ihre Ip-Adresse:</h3>
          <p>' . $ipAddress . '</p>
          <p>------------------------------</p>
          <a target="_blank" href="http://boost-your-life.thats.im/resources/login.php?token=' . $tokenString . '&id=' . $userId . '"><h3>Bestätigen Sie Ihre E-Mail Adresse und aktivieren Sie Ihren Account!</h3></a>
          <br>
          <h3>Falls Sie sich nicht bei uns Registriert haben, so ignorieren Sie diese E-Mail. Solange Sie nicht auf den oberen Link klicken, werden die eingegeben Daten wieder nach 24-72 Stunden gelöscht! Der Link ist 24 Stunden gültig!</h3>
          <br><br>
          <h3>Mit boostastischen Grüßen</h3>
          <h3>Ihr Boost Your Life Team</h3>
          </main>
          </body>
          </html>
          ';

        // Für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
        $header[] = 'MIME-Version: 1.0';
        $header[] = 'Content-type: text/html; charset=utf-8';
        // Zusätzliche Header
        $header[] = 'To: ' . $firstNameInput . ' ' . $lastNameInput . '  <' . $emailInput . '> ';
        $header[] = 'From: Boost-Your-Life <boost-your-life@boost-your-life.thats.im>';

        // Verschicke die E-Mail
        mail($empfaenger, $betreff, $nachricht, implode("\r\n", $header));
    }
    // Schickt eine HTML Mail raus, mit den mitgelieferten Daten
    public function loginDataMail($emailInput, $firstNameInput, $lastNameInput, $ipAddress)
    {
        $empfaenger = $emailInput;
        $betreff = 'Boost-Your-Life --- Ihr Account wurde erfolgreich eingeloggt!';
        $nachricht = '
          <html>
          <head>
          <title>Ihre Daten von dem Login!</title>
          </head>
          <body>
          <main>
          <h1>Ihre Daten von dem Login:</h1>
          <p>------------------------------</p>
          <h3>E-Mail Adresse:</h3>
          <p>' . $emailInput . '</p>
          <h3>Ip-Adresse:</h3>
          <p>' . $ipAddress . '</p>
          <p>------------------------------</p>
          <h3>Sie haben sich nicht eingeloggt? Dann schreiben Sie schnell den Support an!  ---> support@boost-your-life.de</h3>
          <br><br>
          <h3>Mit boostastischen Grüßen</h3>
          <h3>Ihr Boost Your Life Team</h3>
          </main>
          </body>
          </html>
          ';

        // Für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
        $header[] = 'MIME-Version: 1.0';
        $header[] = 'Content-type: text/html; charset=utf-8';
        // Zusätzliche Header
        $header[] = 'To: ' . $firstNameInput . ' ' . $lastNameInput . '  <' . $emailInput . '> ';
        $header[] = 'From: <boost-your-life@boost-your-life.thats.im>';

        // Verschicke die E-Mail
        mail($empfaenger, $betreff, $nachricht, implode("\r\n", $header));
    }
    // Schickt eine HTML Mail raus, mit den mitgelieferten Daten
    public function updateProfileMail($emailInput, $firstNameInput, $lastNameInput, $birthdayInput, $countryInput)
    {
        $empfaenger = $emailInput;
        $betreff = 'Boost-Your-Life --- Ihr Account wurde erfolgreich aktualisiert!';
        $nachricht = '
		<html>
		<head>
		<title>Ihre Daten von der Aktualisieren!</title>
		</head>
		<body>
		<main>
		<h1>Ihre Daten von dem Aktualisieren Ihres Profils:</h1>
		<p>------------------------------</p>
		<h3>Ihr aktualisierter Vorname:</h3>
		<p>' . $firstNameInput . '</p>
		<h3>Ihr aktualisierter Nachname:</h3>
		<p>' . $lastNameInput . '</p>
		<h3>Ihre aktualisierte E-Mail Adresse:</h3>
		<p>' . $emailInput . '</p>
		<h3>Ihre aktualisiertes Geburtsdatum:</h3>
		<p>' . $birthdayInput . '</p>
		<h3>Ihre aktualisiertes Land:</h3>
		<p>' . $countryInput . '</p>
		<p>------------------------------</p>
		<h3>Haben Sie nicht Ihre Daten geändert? Dann schreiben Sie schnell den Support an! ---> support@boost-your-life.de</h3>
		<br><br>
		<h3>Mit boostastischen Grüßen</h3>
		<h3>Ihr Boost Your Life Team</h3>
		</main>
		</body>
		</html>
		';

        // Für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
        $header[] = 'MIME-Version: 1.0';
        $header[] = 'Content-type: text/html; charset=utf-8';
        // Zusätzliche Header
        $header[] = 'To: ' . $firstNameInput . ' ' . $lastNameInput . '  <' . $emailInput . '> ';
        $header[] = 'From: Boost-Your-Life <boost-your-life@boost-your-life.thats.im>';

        // Verschicke die E-Mail
        mail($empfaenger, $betreff, $nachricht, implode("\r\n", $header));
    }

    public function randomPasswordMail($emailInput, $passwordInput)
    {
        $empfaenger = $emailInput;
        $betreff = 'Boost-Your-Life --- Ein Supporter oder Admin hat ihr Passwort abgeändert!';
        $nachricht = '
		<html>
		<head>
		<title>Ein Supporter oder Admin hat ihr Passwort abgeändert!</title>
		</head>
		<body>
		<main>
		<h1>Ein Supporter oder Admin hat ihr Passwort abgeändert:</h1>
		<p>------------------------------</p>
		<h3>Ihr Neues Passwort:</h3>
		<p>' . $passwordInput . '</p>
		<p>------------------------------</p>
		<h3>Bitte ÄNDERN Sie nach dem Login SOFORT Ihr PASSWORT!!!</h3><br>
		<h4>Diese E-Mail haben Sie bekommen:<br>
		1) Wenn mit Ihrem Account etwas nicht stimmt<br>
		2) Wenn Sie eventuell gehackt wurden<br>
		3) Wenn Sie beim Support eingewilligt haben ein Random Password vorzeitig zu bekommen<br>
		4) Wenn Sie überhaupt nicht mehr in Ihren Account reinkommen<br>
		5) Generelle Sicherheitsvorkehrungen</h4>
		<br><br>
		<h3>Mit boostastischen Grüßen</h3>
		<h3>Ihr Boost Your Life Team</h3>
		</main>
		</body>
		</html>
		';

        // für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
        $header[] = 'MIME-Version: 1.0';
        $header[] = 'Content-type: text/html; charset=utf-8';
        // zusätzliche Header
        $header[] = 'To: <' . $emailInput . '> ';
        $header[] = 'From: Boost-Your-Life <boost-your-life@boost-your-life.thats.im>';

        // verschicke die E-Mail
        mail($empfaenger, $betreff, $nachricht, implode("\r\n", $header));
    }
    // Schickt eine HTML Mail raus, mit den mitgelieferten Daten
    public function forgotPasswordMail($emailInput, $userId, $ipAddress, $tokenString)
    {
        $empfaenger = $emailInput;
        $betreff = 'Boost-Your-Life --- Sie haben Ihr Passwort vergessen!';
        $nachricht = '
		<html>
		<head>
		<title>Sie haben Ihr Passwort vergessen</title>
		</head>
		<body>
		<main>
		<h1>Sie haben Ihr Passwort vergessen:</h1>
		<p>------------------------------</p>
		<h3>Ihre E-Mail Adresse:</h3>
		<p>' . $emailInput . '</p>
		<h3>Ihre Ip-Adresse:</h3>
		<p>' . $ipAddress . '</p>
		<br>
		<h3>Bestätigen Sie mit dem Link, dass Sie Ihr Passwort vergessen haben und ein neues wollen! <a target="_blank" href="http://boost-your-life.thats.im/resources/changepassword.php?token=' . $tokenString . '&id=' . $userId . '">Klicken Sie auf den Link</a></h3>
		<p>------------------------------</p>
		<h3>Falls Sie nicht bei uns auf Passwort vergessen geklickt haben, so ignorieren Sie diese E-Mail. Solange Sie nicht auf den oberen Link klicken und dann dort Ihr Passwort abändern, bleibt Ihr altes Passwort weiterhin bestehen. Der Link ist 1 Stunde gültig!</h3>
		<br><br>
		<h3>Mit boostastischen Grüßen</h3>
		<h3>Ihr Boost Your Life Team</h3>
		</main>
		</body>
		</html>
		';

        // Für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
        $header[] = 'MIME-Version: 1.0';
        $header[] = 'Content-type: text/html; charset=utf-8';
        // Zusätzliche Header
        $header[] = 'To: <' . $emailInput . '> ';
        $header[] = 'From: Boost-Your-Life <boost-your-life@boost-your-life.thats.im>';

        // Verschicke die E-Mail
        mail($empfaenger, $betreff, $nachricht, implode("\r\n", $header));
    }
    // Schickt eine HTML Mail raus, mit den mitgelieferten Daten
    public function changePasswordMail($emailInput, $ipAddress)
    {
        $empfaenger = $emailInput;
        $betreff = 'Boost-Your-Life --- Ihr Passwort wurde erfolgreich aktualisiert!';
        $nachricht = '
		<html>
		<head>
		<title>Ihr Passwort wurde erfolgreich aktualisiert!</title>
		</head>
		<body>
		<main>
		<h1>Ihr Passwort wurde aktualisiert:</h1>
		<p>------------------------------</p>
		<h3>Ihre Ip-Adresse:</h3>
		<p>' . $ipAddress . '</p>
		<p>------------------------------</p>
		<h3>Haben Sie nicht Ihr Passwort geändert? Dann schreiben Sie schnell den Support an! ---> support@boost-your-life.de</h3>
		<br><br>
		<h3>Mit boostastischen Grüßen</h3>
		<h3>Ihr Boost Your Life Team</h3>
		</main>
		</body>
		</html>
		';

        // Für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
        $header[] = 'MIME-Version: 1.0';
        $header[] = 'Content-type: text/html; charset=utf-8';
        // Zusätzliche Header
        $header[] = 'To: <' . $emailInput . '> ';
        $header[] = 'From: Boost-Your-Life <boost-your-life@boost-your-life.thats.im>';

        // Verschicke die E-Mail
        mail($empfaenger, $betreff, $nachricht, implode("\r\n", $header));
    }
}
