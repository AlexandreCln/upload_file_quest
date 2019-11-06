<?php

if (!empty($_FILES['files']['name'][0])) {

//    Rendre le tableau à 1 seule dimention :
    $files = $_FILES['files'];

    $uploaded = [];
    $failed = [];

    $allowed = ['jpg', 'pgn', 'gif', 'pdf'];

    foreach ($files['name'] as $position => $fileName) {

        $tmpFile = $files['tmp_name'][$position];
        $sizeFile = $files['size'][$position];
        $errorFile = $files['error'][$position];

//        séparer 'nom_fichier.extention' dans un tableau, puis mettre le dernier mot ( extention ) dans une variable :
        $extFile = explode('.', $fileName);
        $extFile = strtolower(end($extFile));

        if (in_array($extFile, $allowed)) {

            if ($errorFile === 0) {

                if ($sizeFile < 1000000) {

//                    création d'un nom unique avec l'extention
                    $fileNameNew = 'image' . uniqid('', true) . '.' . $extFile;
                    $fileDestination = 'upload/' . $fileNameNew;

                    if (move_uploaded_file($tmpFile, $fileDestination)) {

                        $uploaded[$position] = $fileDestination;

                    } else {
                        $failed[$position] = $fileName . "failed to upload";
                    }

                } else {
                    $failed[$position] = 'Vous devez uploader un fichier de 1Mo maximum';
                }

            } else {
                $failed[$position] = "Erreur sur l'upload de " . $fileName;
            }

        } else {
            $failed[$position] = 'Vous devez uploader un fichier de type png, gif ou jpg.';
        }
    }

    if (!empty($failed)) {
        print_r($failed);
    }

}

$it = new FilesystemIterator('upload/');

if (!empty($_POST['file_path'])) {
    unlink($_POST['file_path']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload File</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous">
</head>
<body>
<!-- formulaire avec enctype et un champ file -->
<section class="upload">
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="files[]" multiple="multiple">
        <input type="submit" value="Send">
    </form>

    <hr>

    <div class="container">
        <div class="row">

          <?php foreach ($it as $fileInfo): ?>
            <div class="col-3">
                <div class="thumbnail">
                    <img src="upload/<?= $fileInfo->getFilename() ?>" alt=".." class="img-thumbnail">
                    <div class="caption">
                        <p><?= $fileInfo->getFilename() ?></p>
                    </div>
                    <form action="" method="post">
                        <input type="hidden" name="file_path" value="upload/<?= $fileInfo->getFilename() ?>">
                        <button type="submit">Delete</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </div>


    <!--    <p>$_FILES['fichier']['name'] Contient le nom d'origine du fichier (sur le poste du client)</p>-->
    <!--    <p>$_FILES['fichier']['tmp_name'] Nom temporaire du fichier dans le dossier temporaire du système (sur le-->
    <!--        serveur)</p>-->
    <!--    <p>$_FILES['fichier']['type'] Contient le type MIME du fichier (plus fiable que l'extension)</p>-->
    <!--    <p>$_FILES['fichier']['size'] Contient la taille du fichier en octets</p>-->
    <!--    <p>$_FILES['fichier']['error'] Contient le code de l'erreur (le cas échéant)</p>-->

</section>
</body>
</html>