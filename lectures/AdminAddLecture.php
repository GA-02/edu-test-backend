<?php

header("Access-Control-Allow-Origin: *");

$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);
$userAdmin = mysqli_query($link, "SELECT * FROM users Where `Email` = '" . $_POST['email'] . "' AND `Password` = '" . htmlspecialchars($_POST['password'], ENT_QUOTES) . "' AND `IDRole` = 1");
if ($userAdmin->num_rows == 0) {
    echo 'Доступ к данному ресурсу есть только у администратора';
    exit;
}
$maxSerialNumber = mysqli_query($link, "SELECT COALESCE(MAX(`SerialNumber`), 0) FROM `lectures` WHERE IFNULL(`IDChapter`, '') = '' AND NOT(`IDStatus` = 3)");
$maxSerialNumber = mysqli_fetch_array($maxSerialNumber)[0];

mysqli_query($link, "INSERT INTO `lectures`(`IDLecture`, `Name`, `IDChapter`, `SerialNumber`, `IDStatus`, `Content`) VALUES (NULL, '', NULL, " . ($maxSerialNumber + 1) . ", 1, '')");
$idChapter = mysqli_insert_id($link);
echo $idChapter;
