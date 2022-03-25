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

$idChapter = $_POST['idChapter'];
$lecturesChapter = mysqli_query($link, "SELECT `IDLecture` FROM `lectures` WHERE `IDChapter`= " . $idChapter . " AND NOT(`lectures`.`IDStatus` = 3)");

if ($lecturesChapter->num_rows != 0) {
    echo ('В этой главе есть лекции. Для удаления глава не должна содержать лекции.');
    exit;
}

$serialNumberDeletedChapter = mysqli_query($link, "SELECT `SerialNumber` FROM `chapters` WHERE `IDChapter` = " . $idChapter);
$serialNumberDeletedChapter = mysqli_fetch_array($serialNumberDeletedChapter)[0];

mysqli_query($link, "DELETE FROM `chapters` WHERE `IDChapter` = " . $idChapter);
mysqli_query($link, "UPDATE `chapters` SET `SerialNumber` = (`SerialNumber` - 1) WHERE `SerialNumber` > " . $serialNumberDeletedChapter);

echo 'Глава успешно удалена';
