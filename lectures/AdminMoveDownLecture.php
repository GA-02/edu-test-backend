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
$idLecture = $_POST['idLecture'];

$infoUpdatedLecture = mysqli_query($link, "SELECT `SerialNumber`, `IDChapter` FROM `lectures` WHERE `IDLecture` = " . $idLecture);
$infoUpdatedLecture = mysqli_fetch_array($infoUpdatedLecture);

$maxSerialNumber = mysqli_query($link, "SELECT MAX(`SerialNumber`) FROM `lectures` WHERE NOT(`IDStatus` = 3) AND `IDChapter` = " . $infoUpdatedLecture[1]);
$maxSerialNumber = mysqli_fetch_array($maxSerialNumber)[0];
if ($infoUpdatedLecture[0] >= $maxSerialNumber) {
    echo 'Лекция последняя в главе';
    exit;
}

mysqli_query($link, "UPDATE `lectures` SET `SerialNumber` = (`SerialNumber` - 1) WHERE NOT(`IDStatus` = 3) AND (`IDChapter` = " . $infoUpdatedLecture[1] . ") AND `SerialNumber` = " . ($infoUpdatedLecture[0] + 1));
mysqli_query($link, "UPDATE `lectures` SET `SerialNumber` = (`SerialNumber` + 1) WHERE `IDLecture` = " . $idLecture);

echo 'Лекция успешно перемещена на одну лекцию ближе к концу главы.';
