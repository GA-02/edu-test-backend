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
$chapterInfo = mysqli_query($link, "SELECT `Name`, `Description` FROM `chapters` WHERE `IDChapter` = " . $idChapter);
$chapterInfo = mysqli_fetch_array($chapterInfo);
$answer['name'] = htmlspecialchars_decode($chapterInfo[0], ENT_QUOTES);
$answer['description'] = htmlspecialchars_decode($chapterInfo[1], ENT_QUOTES);


echo json_encode($answer);
