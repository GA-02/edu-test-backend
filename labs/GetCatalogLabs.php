<?php

header("Access-Control-Allow-Origin: *");

$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$chapters = mysqli_query($link, "SELECT `IDLab`, `StartNumber`, `EndNumber`, `Theme` FROM `labs` WHERE `IDStatus` = 2 ORDER BY `StartNumber`");

while ($chapter = mysqli_fetch_array($chapters)) {
    $answer[] = ['idLab' => $chapter[0], 'startNumber' => $chapter[1], 'endNumber' => $chapter[2], 'theme' => htmlspecialchars_decode($chapter[3], ENT_QUOTES)];
}
echo json_encode($answer);

