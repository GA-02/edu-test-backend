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

$infoDeletedLecture = mysqli_query($link, "SELECT `SerialNumber`, `IDChapter` FROM `lectures` WHERE `IDLecture` = " . $idLecture);
$infoDeletedLecture = mysqli_fetch_array($infoDeletedLecture);

mysqli_query($link, "UPDATE `lectures` SET `IDStatus`= 3 WHERE `IDLecture` = " . $idLecture);

mysqli_query($link, "UPDATE `lectures` SET `SerialNumber` = (`SerialNumber` - 1) WHERE NOT(`IDStatus` = 3) AND (`IDChapter` = " . $infoDeletedLecture[1] . ") AND `SerialNumber` > " . $infoDeletedLecture[0]);

echo 'Лекция успешно удалена';
