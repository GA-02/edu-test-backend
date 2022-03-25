<?php

header("Access-Control-Allow-Origin: *");

$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$link = mysqli_connect($host, $user, $password, $db_name);

$chapters = mysqli_query($link, "SELECT * FROM `chapters` ORDER BY `SerialNumber`");

while ($chapter = mysqli_fetch_array($chapters)) {
    
    $lecturesChapter = mysqli_query($link, "SELECT `IDLecture`, `Name`, CEILING((LENGTH(`Content`)+1)/1500) AS 'TimeReading' FROM `lectures` WHERE `IDChapter` = " . $chapter[0] . " AND `IDStatus` = 2 ORDER BY `SerialNumber`");
    $timeReadChapter = 0;
    while ($lecture = mysqli_fetch_array($lecturesChapter)) {
        $timeReadLecture = $lecture[2];
        $lectures[] = ['idLecture' => $lecture[0], 'nameLecture' => htmlspecialchars_decode($lecture[1], ENT_QUOTES), 'timeReadLecture' => $timeReadLecture];
        $timeReadChapter += $timeReadLecture; 
    }
    if (isset($lectures)){
        $answer[] = ['idChapter' => $chapter[0], 'nameChapter' => htmlspecialchars_decode($chapter[2], ENT_QUOTES), 'description' => htmlspecialchars_decode($chapter[3], ENT_QUOTES), 'timeReadChapter' =>$timeReadChapter, 'lectures' => $lectures];
        unset($lectures);
    }
}
echo json_encode($answer);


