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

$lectureInfo = mysqli_query($link, "SELECT `IDChapter`, `SerialNumber` FROM `lectures` WHERE `IDLecture` = " . $idLecture);
$lectureInfo = mysqli_fetch_array($lectureInfo);
if ($_POST['idChapter'] != $lectureInfo[0]) {

    mysqli_query($link, "UPDATE `lectures` SET `SerialNumber` = (`SerialNumber` - 1) WHERE NOT(`IDStatus` = 3) AND (`IDChapter` = " . $lectureInfo[0] . ") AND `SerialNumber` > " . $lectureInfo[1]);

    $maxSerialNumberInNewChapter = mysqli_query($link, "SELECT COALESCE(MAX(`SerialNumber`), 0) FROM `lectures` WHERE `IDChapter` = " . $_POST['idChapter'] . " AND NOT(`IDStatus` = 3)");
    $maxSerialNumberInNewChapter = mysqli_fetch_array($maxSerialNumberInNewChapter)[0];
    mysqli_query($link, "UPDATE `lectures` SET `Name`='" . htmlspecialchars($_POST['nameLecture'], ENT_QUOTES) . "', `Content`= '" . htmlspecialchars($_POST['content'], ENT_QUOTES) . "', `IDStatus` = " . $_POST['idStatus'] . ", `IDChapter`=" . $_POST['idChapter'] . ", `SerialNumber`=" . ($maxSerialNumberInNewChapter + 1) . " WHERE `IDLecture` = " . $idLecture);
} else {
    mysqli_query($link, "UPDATE `lectures` SET `Name`='" . htmlspecialchars($_POST['nameLecture'], ENT_QUOTES) . "', `Content`= '" . htmlspecialchars($_POST['content'], ENT_QUOTES) . "', `IDStatus` = " . $_POST['idStatus'] . " WHERE `IDLecture` = " . $idLecture);
}
// ,`IDChapter`= " . $_POST['idChapter'] . ", `IDStatus`=" . $_POST['idStatus'] . "
echo "Лекция успешно сохранена";
