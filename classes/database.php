<?php
// Erstellt die Klasse database
class database
{
    // Macht Variablen Privat
    private static $_connections = null;
    private $pdo,
    $query,
    $cfg,
    $dsn,
    $data,
    $id,
    $userid,
    $param,
    $tablename,
    $where,
    $input,
    $password,
    $passwordInput,
    $ipAddress,
    $birthdayInput,
    $countryInput;

    // Versucht eine Verbindung aufzubauen mit dem try and Catch Prinzip. Versuche eine Verbindung herzustellen und wenn es nicht geht, dann bitte zeig ein Fehler an und zerstöre alles
    private function __construct()
    {
        try
        {
            global $cfg;
            $dsn = "mysql:host={$cfg["db_host"]};dbname={$cfg["db_name"]};charset=utf8mb4;port={$cfg["port"]}";
            $this->pdo = new \PDO($dsn, $cfg["db_user"], $cfg["db_password"], array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+00:00'"));

            // Vor dem Live gehen raus machen! WICHTIG!
            // $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        // Wenn es nicht klappt, zeige eine Fehlermeldung an
         catch (PDOException $e) {
            exit('<div style="width: 100vw; height: 100vh; display: flex; text-align: center; align-content: center; align-items: center; flex-wrap: wrap; justify-content: center;">
                <h1 style="width: 90%; color: red;">
                    Ein Fehler ist aufgetreten! Es kann keine Verbindung zur Datenbank aufgebaut werden! Bitte erneut versuchen oder den Support anschreiben! ===> support@boost-your-life.de
                <h1>
            </div>');
        }
    }
    // Schauen ob die Verbindung schon steht, so wird keine neue Verbindung aufgebaut. Wenn keine Verbindung versteht, dann bitte eine aufbauen
    public static function getConnections()
    {
        if (!isset(self::$_connections)) {
            self::$_connections = new database();
        }
        return self::$_connections;
    }
    // --------------------------------------------------
    // CRUD (ADD, EDIT, DELETE AND VIEW)
    // --------------------------------------------------
    // DELETE Funktion für alle Sachen
    public function delete(string $tablename, string $where, array $param = null)
    {
        $query = $this->pdo->prepare("DELETE FROM $tablename WHERE $where");
        $query->execute($param);
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // DELETE Funktion für ID
    public function deleteByID(string $tablename, int $id)
    {
        return $this->delete($tablename, "id=:id", [":id" => $id]);
    }
    // DELETE Funktion für USERID
    public function deleteByUSERID(string $tablename, int $userid)
    {
        return $this->delete($tablename, "userid=:userid", [":userid" => $userid]);
    }
    // VIEW Funktion für alle Sachen
    public function view(string $tablename, string $where, array $param = null)
    {
        if ($param == null) {
            $query = $this->pdo->prepare("SELECT * FROM $tablename WHERE $where");
            $query->execute();
            return $query;
        } else {
            $query = $this->pdo->prepare("SELECT * FROM $tablename WHERE $where");
            $query->execute($param);
            $data = $query->fetch();
            if ($data == true) {
                return $data;
            } else {
                return false;
            }
        }
    }
    //  Die Funkion gibt alles aus, was bei NLP und Images ist
    public function viewNlpImagesInputSearch($input)
    {
        $query = $this->pdo->prepare("SELECT * FROM nlp INNER JOIN images ON nlp.id=images.nlpid WHERE unlocked=true AND (title LIKE '%$input%' OR author LIKE '%$input%' OR description LIKE '%$input%' OR text LIKE '%$input%') ORDER BY nlp.uploaded DESC");
        $query->execute();
        $data = $query->fetchAll();
        if ($data == true) {
            return $data;
        } else {
            return false;
        }
    }
    //  Die Funkion gibt alles aus, was bei NLP und Images ist
    public function viewNlpImages()
    {
        $query = $this->pdo->prepare("SELECT * FROM nlp INNER JOIN images ON nlp.id=images.nlpid WHERE unlocked=true ORDER BY nlp.uploaded DESC");
        $query->execute();
        $data = $query->fetchAll();
        if ($data == true) {
            return $data;
        } else {
            return false;
        }
    }
    // VIEW Funktion für ID
    public function viewByID(string $tablename, int $id)
    {
        return $this->view($tablename, "id=:id", [":id" => $id]);
    }
    // VIEW Funktion für USERID
    public function viewByUSERID(string $tablename, int $userid)
    {
        return $this->view($tablename, "userid=:userid", [":userid" => $userid]);
    }
    public function viewAllUserID(string $tablename, string $where)
    {
        $query = $this->pdo->prepare("SELECT userid FROM $tablename WHERE $where");
        $query->execute();

        return $query->fetchAll();
    }
    // Die Funktion gibt alles aus, was in der "nlp" Tabelle drin ist
    public function viewNLPSearch($input, $id, $rights)
    {
        if ($rights > 31) {
            $query = $this->pdo->prepare("SELECT * FROM nlp WHERE title LIKE '%$input%' OR description LIKE '%$input%' OR text LIKE '%$input%' ");
        } else {
            $query = $this->pdo->prepare("SELECT * FROM nlp WHERE userid=$id AND (title LIKE '%$input%' OR description LIKE '%$input%' OR text LIKE '%$input%') ");
        }
        $query->execute();
        return $query;
    }
    // Diese Funktion gibt alles aus, was in den beiden "userdata" und "userinfo" Tabellen drin ist aus
    public function viewAdminUsersSearch($input, int $userRights)
    {
        $query = $this->pdo->prepare("SELECT * FROM userdata INNER JOIN userinfo ON userdata.id=userinfo.userid WHERE rights<$userRights AND (firstname LIKE '%$input%' OR lastname LIKE '%$input%' OR email LIKE '%$input%') ");
        $query->execute();
        return $query;
    }
    // Die Funktion updatet alles was bei den Übungen geändert wird
    public function updateDataNlp(string $tableName, $id, $titleInput, $descriptionInput, $textInput, $genre, $soundcloudInput, $youtubeInput, $unlocked)
    {
        if ($soundcloudInput == "" && $youtubeInput == "") {
            $query = $this->pdo->prepare("UPDATE $tableName SET
            title=:title,
            description=:description,
            text=:text,
            genre=:genre,
            unlocked=:unlocked,
            soundcloud=null,
            youtube=null
            WHERE id=:id");

            $query->execute(array(':id' => $id, ':title' => $titleInput, ':description' => $descriptionInput, ':text' => $textInput, ':genre' => $genre, ':unlocked' => $unlocked));
        } elseif ($soundcloudInput == "") {
            $query = $this->pdo->prepare("UPDATE $tableName SET
            title=:title,
            description=:description,
            text=:text,
            genre=:genre,
            youtube=:youtube,
            unlocked=:unlocked,
            soundcloud=null
            WHERE id=:id");

            $query->execute(array(':id' => $id, ':title' => $titleInput, ':description' => $descriptionInput, ':text' => $textInput, ':genre' => $genre, ':youtube' => $youtubeInput, ':unlocked' => $unlocked));
        } elseif ($youtubeInput == "") {
            $query = $this->pdo->prepare("UPDATE $tableName SET
            title=:title,
            description=:description,
            text=:text,
            genre=:genre,
            soundcloud=:soundcloud,
            unlocked=:unlocked,
            youtube=null
            WHERE id=:id");

            $query->execute(array(':id' => $id, ':title' => $titleInput, ':description' => $descriptionInput, ':text' => $textInput, ':genre' => $genre, ':soundcloud' => $soundcloudInput, ':unlocked' => $unlocked));
        } else {
            $query = $this->pdo->prepare("UPDATE $tableName SET
            title=:title,
            description=:description,
            text=:text,
            genre=:genre,
            soundcloud=:soundcloud,
            youtube=:youtube,
            unlocked=:unlocked
            WHERE id=:id");

            $query->execute(array(':id' => $id, ':title' => $titleInput, ':description' => $descriptionInput, ':text' => $textInput, ':genre' => $genre, ':soundcloud' => $soundcloudInput, ':youtube' => $youtubeInput, ':unlocked' => $unlocked));
        }
        // Hat alles funktioniert, return query. Andernfalls false
        if ($query == true) {
            return $query;
        } else {
            return false;
        }

    }
    // Die Funktion updatet den Status der Übungen
    public function updateStatusNLP(string $tablename, $id, $unlocked)
    {
        $query = $this->pdo->prepare("UPDATE $tablename SET unlocked=:unlocked
        WHERE id=:id");
        $query->execute(array(':id' => $id, ':unlocked' => $unlocked));
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die Funktion updatet die NlId der Images
    public function updateNlpImageID($nlpid, $id)
    {
        $query = $this->pdo->prepare("UPDATE images SET nlpid=:nlpid
        WHERE id=:id ORDER BY uploaded DESC");
        $query->execute(array(':id' => $id, ':nlpid' => $nlpid));
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die Funktion updatet das Passwort des Users
    public function updateAdminRandomPassword(string $tablename, $id, $password)
    {
        $query = $this->pdo->prepare("UPDATE $tablename SET
        password=:password
        WHERE id=:id");

        $query->execute(array(':id' => $id, ':password' => $password));
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die Funktion updatet die editierten Input Sachen des Users
    public function updateAdminUserData(string $tablename, $id, $firstnameInput, $lastnameInput, $emailInput, $rights)
    {
        if ($tablename == "userdata") {
            $query = $this->pdo->prepare("UPDATE $tablename SET
                firstname=:firstname,
                lastname=:lastname,
                email=:email
                WHERE id=:id");

            $query->execute(array(':id' => $id, ':firstname' => $firstnameInput, ':lastname' => $lastnameInput, ':email' => $emailInput));
        } else {
            $query = $this->pdo->prepare("UPDATE $tablename SET
                rights=:rights
                WHERE userid=:userid");

            $query->execute(array(':userid' => $id, ':rights' => $rights));
        }
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die Funktion updatet den Status des Users
    public function updateStatusAdmin(string $tablename, $id, $status)
    {
        $query = $this->pdo->prepare("UPDATE $tablename SET status=:status
        WHERE userid=:userid");
        $query->execute(array(':userid' => $id, ':status' => $status));
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die Funktion updatet das Passwort des Users in die "userdata" Tabelle
    public function updateChangePassword(string $tablename, int $id, $password)
    {
        $query = $this->pdo->prepare("UPDATE $tablename SET
        password=:password
        WHERE id=:id");
        $query->execute(array(':id' => $id, ':password' => $password));
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die Funktion updatet das Profil des Users in die "userdata" Tabelle
    public function updateProfileUserData(string $tablename, $id, $firstnameInput, $lastnameInput, $emailInput, $birthdayInput, $countryInput)
    {
        $query = $this->pdo->prepare("UPDATE $tablename SET
        firstname=:firstname,
        lastname=:lastname,
        email=:email,
        birthday=:birthday,
        country=:country
        WHERE id=:id");
        $query->execute(array(':id' => $id, ':firstname' => $firstnameInput, ':lastname' => $lastnameInput, ':email' => $emailInput, ':birthday' => $birthdayInput, ':country' => $countryInput));
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die Funktion updatet nach dem Login die "lastlogin" und "ipaddress" Spalte in der "userinfo" Tabelle
    public function updateLoginUserInfo(string $tablename, int $userId, $lastLogin, $ipAddress)
    {
        $query = $this->pdo->prepare("UPDATE $tablename SET
        lastlogin=:lastlogin,
        ipaddress=:ipaddress
            WHERE userid=:userid");
        $query->execute(array(':userid' => $userId, ':lastlogin' => $lastLogin, ':ipaddress' => $ipAddress));
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die Funktion updatet die "active" Spalte in der "userinfo" Tabelle. So wird der Account aktiviert
    public function updateRegisterAccountActivate(string $tablename, int $userId, bool $active)
    {
        $query = $this->pdo->prepare("UPDATE $tablename SET
        active=:active
        WHERE userid=:userid");
        $query->execute(array(':userid' => $userId, ':active' => $active));
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die Funktion fügt neue NLP Übungen zu der "nlp" Tabelle
    public function insertDataNLP(string $tablename, $userid, $author, $genre, $titleInput, $descriptionInput, $textInput, $soundcloudInput, $youtubeInput)
    {
        if ($soundcloudInput == "" && $youtubeInput == "") {
            $query = $this->pdo->prepare("INSERT INTO $tablename SET
            author=:author,
            userid=:userid,
            genre=:genre,
            title=:title,
            description=:description,
            text=:text");
            $query->execute(array(':userid' => $userid, ':author' => $author, ':genre' => $genre, ':title' => $titleInput, ':description' => $descriptionInput, ':text' => $textInput));
        } elseif ($soundcloudInput == "") {
            $query = $this->pdo->prepare("INSERT INTO $tablename SET
            author=:author,
            userid=:userid,
            genre=:genre,
            title=:title,
            description=:description,
            text=:text,
            youtube=:youtube");
            $query->execute(array(':userid' => $userid, ':author' => $author, ':genre' => $genre, ':title' => $titleInput, ':description' => $descriptionInput, ':text' => $textInput, ':youtube' => $youtubeInput));
        } elseif ($youtubeInput == "") {
            $query = $this->pdo->prepare("INSERT INTO $tablename SET
            author=:author,
            userid=:userid,
            genre=:genre,
            title=:title,
            description=:description,
            text=:text,
            soundcloud=:soundcloud");
            $query->execute(array(':userid' => $userid, ':author' => $author, ':genre' => $genre, ':title' => $titleInput, ':description' => $descriptionInput, ':text' => $textInput, ':soundcloud' => $soundcloudInput));
        } else {
            $query = $this->pdo->prepare("INSERT INTO $tablename SET
            author=:author,
            userid=:userid,
            genre=:genre,
            title=:title,
            description=:description,
            text=:text,
            soundcloud=:soundcloud,
            youtube=:youtube");
            $query->execute(array(':userid' => $userid, ':author' => $author, ':genre' => $genre, ':title' => $titleInput, ':description' => $descriptionInput, ':text' => $textInput, ':soundcloud' => $soundcloudInput, ':youtube' => $youtubeInput));
        }
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die funktion lädt die ganzen User Datas in die "userdata" Tabelle hoch
    public function insertRegisterUserData(string $tablename, $firstnameInput, $lastnameInput, $emailInput, $passwordInput)
    {
        $query = $this->pdo->prepare("INSERT INTO $tablename SET
        firstname=:firstname,
        lastname=:lastname,
        email=:email,
        password=:password");
        $query->execute(array(':firstname' => $firstnameInput, ':lastname' => $lastnameInput, ':email' => $emailInput, ':password' => $passwordInput));
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die funktion lädt die ganzen User Infos in die "userinfo" Tabelle hoch
    public function insertRegisterUserInfo(string $tablename, int $userId, $ipAddress)
    {
        $query = $this->pdo->prepare("INSERT INTO $tablename SET
        userid=:userid,
        ipaddress=:ipaddress");
        $query->execute(array(':userid' => $userId, ':ipaddress' => $ipAddress));
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
    // Die Funktion lädt die Tokens hoch in die "token" Tabelle
    public function tokenInsert($tokenString, $tokenType, $tokenExpires, $userId)
    {
        if ($userId == null) {
            $query = $this->pdo->prepare("INSERT INTO token SET
            tokenstring=:tokenstring,
            type=:type,
            expires=:expires");
            $query->execute(array(':tokenstring' => $tokenString, ':type' => $tokenType, ':expires' => $tokenExpires));
        } else {
            $query = $this->pdo->prepare("INSERT INTO token SET
            tokenstring=:tokenstring,
            type=:type,
            expires=:expires,
            userid=:userid");
            $query->execute(array(':tokenstring' => $tokenString, ':type' => $tokenType, ':expires' => $tokenExpires, ':userid' => $userId));
        }
        // Hat alles funktioniert, return die variable. Andernfalls false
        if ($query == true) {
            return $query;
        } else {
            return false;
        }
    }
    // Die Funktion fügt die Bilder in die "images" Tabelle
    public function insertImage(string $tableName, int $userId, $name, $type, $size, $src)
    {
        $query = $this->pdo->prepare("INSERT INTO images SET
        userid=:userid,
        tablename=:tablename,
        name=:name,
        type=:type,
        size=:size,
        src=:src");
        $query->execute(array(':tablename' => $tableName, ':userid' => $userId, ':name' => $name, ':type' => $type, ':size' => $size, ':src' => $src));
        // Hat alles funktioniert, return true. Andernfalls false
        if ($query == true) {
            return true;
        } else {
            return false;
        }
    }
}
