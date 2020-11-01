<?php
// Diese Funktion schaut ob eine Datei hochgeladen wurde und validiert diese.
function imageUploads($status, $tableName, $userId, $nlpid, $unlink)
{
    if ($status == "insert") {
        // Wenn die File abgeschickt wurde, also nicht leer ist, dann führe den Code unten aus
        // Die error Variable wird auf false gesetzt, falche Dateien hochlädt, wird die Variable auf true gesetzt und so kommt man nicht weiter
        if (!empty($_FILES['appImage'])) {
            $error = false;

            // Der FileName, die Extension und das Array mit den Endungen werden in Variablen gespeichert
            $fileName = pathinfo($_FILES['appImage']['name'], PATHINFO_FILENAME);
            $extension = strtolower(pathinfo($_FILES['appImage']['name'], PATHINFO_EXTENSION));
            $allowedImageEnding = array('png', 'jpg', 'jpeg');

            // Schaut ob die Endungen in dem FileName vorhanden ist, falls nicht, kommt eine Fehlermeldung
            if (!in_array($extension, $allowedImageEnding)) {
                global $errorImage;
                echo $errorImage = '<p class="errorMessages">Ungültige Dateiendung. Nur png, jpg, jpeg Dateien sind erlaubt!</p>';
                $error = true;
            }

            // Die FileGröße und die maximale KB Größe werden in Variablen gespeichert
            $fileSize = $_FILES['appImage']['size'];
            $maxSize = 10000 * 1024;

            // Ist die FileGröße grüßer als die maximale Größe, dann zeige bitte eine Fehlermeldung an
            if ($fileSize > $maxSize) {
                global $errorImage;
                echo $errorImage = '<p class="errorMessages">Die Datei ist zu groß. Maximal sind 10.000kb/10mb erlaubt!</p>';
                $error = true;
            }

            // Speichert den FilePfad, UploadOrdner und den neuen Pfad in Variablen
            $filePath = $_FILES['appImage']['tmp_name'];
            $uploadFolder = '..//db-images/';
            $newPath = $uploadFolder . $fileName . '.' . $extension;

            // Wenn der neue Pfad schon existiert, dann füge hintenr den FileNamen eine Nummer dran und wiederhole das die ganze Zeit
            // test, test#1, test#2, test#3, etc.
            if (file_exists($newPath)) {
                $id = 1;
                do {
                    $newPath = $uploadFolder . $fileName . '--' . $id . '.' . $extension;
                    $id++;
                } while (file_exists($newPath));
            }

            // Die SRC für HTML Image Tags, diese muss man in die Datenbank abspeichern, damit man später ein Bild ausgeben kann
            $src = "db-images/" . $fileName . '.' . $extension;

            // Wenn oben alles richtig ist und kein $error = true ist, dann bewege die Datei in den Upload Ordner mit dem Namen, wie die Datei hochgeladen wurde
            if (!$error) {
                move_uploaded_file($filePath, $newPath);
                // Bild hochladen
                database::getConnections()->insertImage($tableName, $userId, $fileName, $extension, $fileSize, $src);
            }
            return $error;
        }
    } else {
        // Wenn die File abgeschickt wurde, also nicht leer ist, dann führe den Code unten aus
        // Die error Variable wird auf false gesetzt, falche Dateien hochlädt, wird die Variable auf true gesetzt und so kommt man nicht weiter
        if (!empty($_FILES['appImage'])) {
            $error = false;

            // Der FileName, die Extension und das Array mit den Endungen werden in Variablen gespeichert
            $fileName = pathinfo($_FILES['appImage']['name'], PATHINFO_FILENAME);
            $extension = strtolower(pathinfo($_FILES['appImage']['name'], PATHINFO_EXTENSION));
            $allowedImageEnding = array('png', 'jpg', 'jpeg');

            // Schaut ob die Endungen in dem FileName vorhanden ist, falls nicht, kommt eine Fehlermeldung
            if (!in_array($extension, $allowedImageEnding)) {
                global $errorImage;
                echo $errorImage = '<p class="errorMessages">Ungültige Dateiendung. Nur png, jpg, jpeg Dateien sind erlaubt!</p>';
                $error = true;
            }

            // Die FileGröße und die maximale KB Größe werden in Variablen gespeichert
            $fileSize = $_FILES['appImage']['size'];
            $maxSize = 5000 * 1024;

            // Ist die FileGröße grüßer als die maximale Größe, dann zeige bitte eine Fehlermeldung an
            if ($fileSize > $maxSize) {
                global $errorImage;
                echo $errorImage = '<p class="errorMessages">Die Datei ist zu groß. Maximal sind 10.000kb/10mb erlaubt!</p>';
                $error = true;
            }

            // Speichert den FilePfad, UploadOrdner und den neuen Pfad in Variablen
            $filePath = $_FILES['appImage']['tmp_name'];
            $uploadFolder = '..//db-images/';
            $newPath = $uploadFolder . $fileName . '.' . $extension;

            // Wenn der neue Pfad schon existiert, dann füge hinter den FileNamen eine Nummer dran und wiederhole das die ganze Zeit
            if (file_exists($newPath)) {
                $id = 1;
                do {
                    $newPath = $uploadFolder . $fileName . '--' . $id . '.' . $extension;
                    $id++;
                } while (file_exists($newPath));
            }

            // Die SRC für HTML Image Tags, diese muss man in die Datenbank abspeichern, damit man später ein Bild ausgeben kann
            $src = "db-images/" . $fileName . '.' . $extension;

            // Wenn oben alles richtig ist und kein $error = true ist, dann bewege die Datei in den Upload Ordner mit dem Namen, wie die Datei hochgeladen wurde
            if (!$error) {
                move_uploaded_file($filePath, $newPath);
                // Wenn die NLP ID Leer ist, dann ist es dass Profile hochladen, wenn es nicht leer ist, dann ist es die Nlp Übung hochladen
                if (empty($nlpid)) {
                    database::getConnections()->delete("images", "userid=:userid AND tablename=:tablename", [':userid' => $userId, ':tablename' => 'Profile']);
                } else {
                    database::getConnections()->delete("images", "userid=:userid AND nlpid=:nlpid", [":userid" => $userId, ":nlpid" => $nlpid]);
                }
                // Bild hochladen
                database::getConnections()->insertImage($tableName, $userId, $fileName, $extension, $fileSize, $src);
                unlink($unlink);
            }
            return $error;
        }
    }
}
