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
// $idLecture = 1;
$lectureInfo = mysqli_query($link, "SELECT `Name`, `IDChapter`, `IDStatus`, `Content` FROM `lectures` WHERE `IDLecture` = " . $idLecture);
$lectureInfo = mysqli_fetch_array($lectureInfo);
$answer['name'] = htmlspecialchars_decode($lectureInfo[0], ENT_QUOTES);
$answer['idChapter'] = $lectureInfo[1];
$answer['content'] = htmlspecialchars_decode($lectureInfo[3], ENT_QUOTES);
$answer['idStatus'] = $lectureInfo[2];

$chapters = mysqli_query($link, "SELECT * FROM `chapters` ORDER BY `SerialNumber`");

while ($chapter = mysqli_fetch_array($chapters)) {
    $answer['chapters'][] = ['idChapter' => $chapter[0], 'nameChapter' => htmlspecialchars_decode($chapter[2], ENT_QUOTES)];
}

echo json_encode($answer);
