<?php
// Erstellt die Klasse validation
class validation
{
    // Macht Variablen Privat
    private $_SESSION,
    $error,
    $data,
    $input,
    $linkInput,
    $firstnameInput,
    $lastnameInput,
    $emailInput,
    $passwordInput,
    $passwordRepeatInput,
    $checkboxInput,
    $messageInput,
    $titleInput,
    $descriptionInput,
    $textInput,
    $youtubeInput,
    $soundcloudInput;

    // Die Funktion schaut ob das eingegebene Passwort mit dem Password von einer Tabelle übereinstimmt
    public function validPasswordVerify($data, $passwordInput)
    {
        if (password_verify($passwordInput, $data['password'])) {
            return true;
        } else {
            $_SESSION['loginCount']++;
            global $errorLogin;
            $errorLogin = '<p class="errorMessages">E-Mail oder Passwort sind falsch! Versuchen Sie es noch einmal erneut!</p>';
        }
    }
    // Schaut ob beim Registrieren eines Users alles richtig eingegeben wurde
    public function validRegister($firstnameInput, $lastnameInput, $emailInput, $passwordInput, $passwordRepeatInput, $checkboxInput)
    {
        if ($this->validFirstName($firstnameInput) == true && $this->validLastName($lastnameInput) == true && $this->validEmail($emailInput) == true && $this->validPassword($passwordInput) == true && $this->validPasswordRepeat($passwordInput, $passwordRepeatInput) == true && $this->validCheckbox($checkboxInput) == true) {
            return true;
        } else {
            return false;
        }
    }
    // Schaut ob beim Updaten des Profiles alles richtig eingegeben wurde
    public function validProfile($firstnameInput, $lastnameInput, $emailInput)
    {
        if ($this->validFirstName($firstnameInput) == true && $this->validLastName($lastnameInput) == true && $this->validEmail($emailInput) == true) {
            return true;
        } else {
            return false;
        }
    }
    // Schaut ob beim Passwort Vergessen alles richtig eingegeben wurde
    public function validForgotPassword($firstnameInput, $lastnameInput, $emailInput)
    {
        if ($this->validFirstName($firstnameInput) == true && $this->validLastName($lastnameInput) == true && $this->validEmail($emailInput) == true) {
            return true;
        } else {
            return false;
        }
    }
    // Schaut ob beim Passwort Ändern alles richtig eingegeben wurde
    public function validChangePassword($passwordInput, $passwordRepeatInput)
    {
        if ($this->validPassword($passwordInput) == true && $this->validPasswordRepeat($passwordInput, $passwordRepeatInput) == true) {
            return true;
        } else {
            return false;
        }
    }
    // Schaut ob beim Updaten der Übungen alles richtig eingegeben wurde
    public function validUpdateNLP($titleInput, $descriptionInput, $textInput, $soundcloudInput, $youtubeInput)
    {
        if ($soundcloudInput == "" && $youtubeInput == "") {
            if ($this->validTitle($titleInput) == true && $this->validDescription($descriptionInput) == true && $this->validTextMessage($textInput) == true) {
                return true;
            } else {
                return false;
            }
        } elseif ($soundcloudInput == "") {
            if ($this->validTitle($titleInput) == true && $this->validDescription($descriptionInput) == true && $this->validTextMessage($textInput) == true && $this->validYoutubeLink($youtubeInput) == true) {
                return true;
            } else {
                return false;
            }
        } elseif ($youtubeInput == "") {
            if ($this->validTitle($titleInput) == true && $this->validDescription($descriptionInput) == true && $this->validTextMessage($textInput) == true && $this->validSoundcloudLink($soundcloudInput) == true) {
                return true;
            } else {
                return false;
            }
        } else {
            if ($this->validTitle($titleInput) == true && $this->validDescription($descriptionInput) == true && $this->validTextMessage($textInput) == true && $this->validSoundcloudLink($soundcloudInput) == true && $this->validYoutubeLink($youtubeInput) == true) {
                return true;
            } else {
                return false;
            }
        }
    }
    // Schaut ob beim Importieren neuer Übungen alles richtig eingegeben wurde
    public function validRegisterNLP($titleInput, $descriptionInput, $textInput, $genreInput, $soundcloudInput, $youtubeInput, $checkboxInput)
    {
        if ($soundcloudInput == "" && $youtubeInput == "") {
            if ($this->validTitle($titleInput) == true && $this->validDescription($descriptionInput) == true && $this->validTextMessage($textInput) == true && $this->validSections($genreInput) == true && $this->validCheckbox($checkboxInput) == true) {
                return true;
            } else {
                return false;
            }
        } elseif ($soundcloudInput == "") {
            if ($this->validTitle($titleInput) == true && $this->validDescription($descriptionInput) == true && $this->validTextMessage($textInput) == true && $this->validSections($genreInput) == true && $this->validYoutubeLink($youtubeInput) == true && $this->validCheckbox($checkboxInput) == true) {
                return true;
            } else {
                return false;
            }
        } elseif ($youtubeInput == "") {
            if ($this->validTitle($titleInput) == true && $this->validDescription($descriptionInput) == true && $this->validTextMessage($textInput) == true && $this->validSections($genreInput) == true && $this->validSoundcloudLink($soundcloudInput) == true && $this->validCheckbox($checkboxInput) == true) {
                return true;
            } else {
                return false;
            }
        } else {
            if ($this->validTitle($titleInput) == true && $this->validDescription($descriptionInput) == true && $this->validTextMessage($textInput) == true && $this->validSections($genreInput) == true && $this->validSoundcloudLink($soundcloudInput) == true && $this->validYoutubeLink($youtubeInput) == true && $this->validCheckbox($checkboxInput) == true) {
                return true;
            } else {
                return false;
            }
        }
    }
    // Validiert den Vornamen
    public function validFirstName($firstnameInput)
    {
        $error = true;
        if (!preg_match("/^[a-zäöüÄÖÜ ,.'-]{2,75}$/i", $firstnameInput)) {
            global $errorFirstName;
            $errorFirstName = '<p class="errorMessages">Bitte geben Sie einen richtigen Vornamen an!</p>';
            $error = false;
        }
        return $error;
    }
    // Validiert den Nachnamen
    public function validLastName($lastnameInput)
    {
        $error = true;
        if (!preg_match("/^[a-zäöüÄÖÜ ,.'-]{2,75}$/i", $lastnameInput)) {
            global $errorLastName;
            $errorLastName = '<p class="errorMessages">Bitte geben Sie einen richtigen Nachnamen an!</p>';
            $error = false;
        }
        return $error;
    }
    // Validiert die E-Mail Adresse
    public function validEmail($emailInput)
    {
        $error = true;
        if (!preg_match('/^[a-zäöüÄÖÜ0-9_.,+-]{2,200}@[a-z0-9.-]{2,35}.[a-z]{2,20}$/i', $emailInput)) {
            global $errorEmail;
            $errorEmail = '<p class="errorMessages">Bitte geben Sie eine richtige E-Mail Adresse an!</p>';
            $error = false;
        }
        return $error;
    }
    // Validiert das Passwort
    public function validPassword($passwordInput)
    {
        $error = true;
        if (!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{10,100}$/', $passwordInput)) {
            global $errorPassword;
            $errorPassword = '<p class="errorMessages">Bitte geben Sie ein richtiges Passwort ein! Das Passwort muss einen klein Buchstaben, groß Buchstaben, eine Zahl und ein Sonderzeichen enthalten! Mindestens müssen es 10 Zeichen sein!';
            $error = false;
        }
        return $error;
    }
    // Validiert das wiederholte Passwort
    public function validPasswordRepeat($passwordInput, $passwordRepeatInput)
    {
        $error = true;
        if ($passwordInput !== $passwordRepeatInput || empty($passwordRepeatInput)) {
            global $errorPasswordRepeat;
            $errorPasswordRepeat = '<p class="errorMessages">Das Passwort muss mit den oberen Passwort übereinstimmen!</p>';
            $error = false;
        }
        return $error;
    }
    // Validiert allgemeine Textarea Boxen
    public function validTextMessage($messageInput)
    {
        $error = true;
        if (!preg_match('/^[a-zäöüÄÖÜ0-9_.,\'#%()ß:;=!?+ -]{30,8000}$/i', $messageInput)) {
            global $errorTextMessage;
            $errorTextMessage = '<p class="errorMessages">Bitte geben Sie einen Text ein, der mindestens 30 Zeichen besitzt!</p>';
            $error = false;
        }
        return $error;
    }
    // Validiert die AGB Checkbox
    public function validCheckbox($checkboxInput)
    {
        $error = true;
        if (empty($checkboxInput)) {
            global $errorAGBCheckbox;
            $errorAGBCheckbox = '<p class="errorMessages">Bitte akzeptieren Sie die AGBS und den Datenschutz!</p>';
            $error = false;
        }
        return $error;
    }
    // Validiert den Soundcloud Link der NLP Übungen
    public function validSoundcloudLink($linkInput)
    {
        $error = true;
        if (!filter_var($linkInput, FILTER_VALIDATE_URL)) {
            global $errorSoundcloudLink;
            $errorSoundcloudLink = '<p class="errorMessages">Bitte geben Sie einen richtigen Soundcloud Link an!</p>';
            $error = false;
        }
        return $error;
    }
    // Validiert den Youtube Link der NLP Übungen
    public function validYoutubeLink($linkInput)
    {
        $error = true;
        if (!filter_var($linkInput, FILTER_VALIDATE_URL)) {
            global $errorYouTubeLink;
            $errorYouTubeLink = '<p class="errorMessages">Bitte geben Sie einen richtigen YouTube Link an!</p>';
            $error = false;
        }
        return $error;
    }
    // Validiert den Titel der NLP Übungen
    public function validTitle($input)
    {
        $error = true;
        if (!preg_match("/^[a-zäöüÄÖÜ0-9 ,.'_ +():;!?ß-]{10,45}$/i", $input)) {
            global $errorTitle;
            $errorTitle = '<p class="errorMessages">Bitte geben Sie einen richtigen Titel an!</p>';
            $error = false;
        }
        return $error;
    }
    // Validiert die Beschreibung der NLP Übungen
    public function validDescription($input)
    {
        $error = true;
        if (!preg_match("/^[a-zäöüÄÜÖ0-9 ,.'_ +()=:;!?ß-]{10,200}$/i", $input)) {
            global $errorDescription;
            $errorDescription = '<p class="errorMessages">Bitte geben Sie eine richtige Beschreibung an!</p>';
            $error = false;
        }
        return $error;
    }
    // Validiert die Sections
    public function validSections($input)
    {
        $error = true;
        if (empty($input)) {
            global $errorSections;
            $errorSections = '<p class="errorMessages">Bitte wählen Sie etwas aus!</p>';
            $error = false;
        }
        return $error;
    }
}
