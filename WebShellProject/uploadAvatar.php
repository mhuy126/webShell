<?php
require_once "pdo.php";
require "profile.php";

if (!isset($_POST['upload']) && !isset($_POST['avatar'])) {
    die("No file choosen");
}

$filepath = $_FILES['avatar']['tmp_name'];
$fileSize = filesize($filepath);
$fileinfo = finfo_open(FILEINFO_MIME_TYPE);
$filetype = finfo_file($fileinfo, $filepath);


if ($fileSize === 0) {
    die("File is empty");
}

$MAX_FILE_SIZE = 1024 * 1024 * 1024 * 3;

if ($fileSize > $MAX_FILE_SIZE) {
    var_dump($_FILES['avatar']);
    die("File is too large");
}

$allowTypes = [
    "image/png" => 'png',
    'image/jpeg' => 'jpg'
];

if (!in_array($filetype, array_keys($allowTypes))) {
    die("File is not allowed");
}

//Move the file
$filename = basename($filepath);
$extension = $allowTypes[$filetype];
$targetDirectory = __DIR__ . "/uploads";
$newFilePath = $targetDirectory . "/" . $filename . "." . $extension;

// if (!copy($filepath, $newFilePath)) {
//     die("Move file error");
// }

if (move_uploaded_file($filepath, $newFilePath)) {
    // $image_base64 = base64_encode(file_get_contents($newFilePath));
    // $image =
    try {

        $sql_upload = "INSERT INTO avatars (username, avatar, u_id) VALUES (:u, :a, :id)";
        $stmt_upload = $pdo->prepare($sql_upload);
        $stmt_upload->execute(array(
            ":u" => $cur_uname,
            ":a" => $filename . "." . $extension,
            ":id" => $cur_id
        ));
        header("Location: profile.php");
        return;
    } catch (PDOException $e) {
        $_SESSION['error'] = $e;
        header("Location: profile.php");
        return;
    }
}

// unlink($filepath); //Delete the temp file