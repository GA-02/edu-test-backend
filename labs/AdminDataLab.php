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
$idLab = $_POST['idLab'];
// $idLab = 1;
// $idLecture = 1;
$labInfo = mysqli_query($link, "SELECT `StartNumber`, `EndNumber`, `Theme`, `Goal`, `Equipment`, `Content`, `IDStatus` FROM `labs` WHERE `IDLab` = " . $idLab);
$labInfo = mysqli_fetch_array($labInfo);
$answer['startNumber'] = $labInfo[0];
$answer['endNumber'] = $labInfo[1];
$answer['theme'] = htmlspecialchars_decode($labInfo[2], ENT_QUOTES);
$answer['goal'] = htmlspecialchars_decode($labInfo[3], ENT_QUOTES);
$answer['equipment'] = htmlspecialchars_decode($labInfo[4], ENT_QUOTES);
$answer['content'] = htmlspecialchars_decode($labInfo[5], ENT_QUOTES);
$answer['idStatus'] = $labInfo[6];

echo json_encode($answer);
