<?php

header("Access-Control-Allow-Origin: *");

$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$lecture = mysqli_query($link, "SELECT `lectures`.`Name`, `lectures`.`Content`, `lectures`.`IDChapter`, `chapters`.`Name`, `chapters`.`SerialNumber` FROM `lectures` INNER JOIN `chapters` ON `lectures`.`IDChapter` = `chapters`.`IDChapter` WHERE `lectures`.`IDLecture` = " . $_POST['idLecture'] . " AND `lectures`.`IDStatus` = 2 ORDER BY `lectures`.`SerialNumber`");
$lecture = mysqli_fetch_array($lecture);
$answer['name'] = htmlspecialchars_decode($lecture[0], ENT_QUOTES);
$answer['content'] = htmlspecialchars_decode($lecture[1], ENT_QUOTES);
$answer['nameChapter'] =  htmlspecialchars_decode($lecture[3], ENT_QUOTES);
$lecturesInChapter = mysqli_query($link, "SELECT `IDLecture`, `Name`, CEILING((LENGTH(`Content`)+1)/1500) AS 'TimeReading' FROM `lectures` WHERE `IDChapter` = " . $lecture[2] . " AND `IDStatus` = 2 ORDER BY `SerialNumber`");
while ($lectureInChapter = mysqli_fetch_array($lecturesInChapter)) {
    $answer['lecturesInChapter'][] = ['idLecture' => $lectureInChapter[0], 'name' => htmlspecialchars_decode($lectureInChapter[1], ENT_QUOTES), 'timeReading' => $lectureInChapter[2]];
}

$moveChapters = mysqli_query($link, "SELECT `chapters`.`IDChapter`, `chapters`.`Name`, `lectures`.`IDLecture` FROM `chapters` INNER JOIN `lectures` ON `chapters`.`IDChapter` = `lectures`.`IDChapter` WHERE ((`chapters`.`SerialNumber` = " . ($lecture[4] - 1) . ") OR (`chapters`.`SerialNumber` = " . ($lecture[4] + 1) . ")) AND `lectures`.`SerialNumber` = 1 ORDER BY `chapters`.`SerialNumber` DESC");
while ($moveChapter = mysqli_fetch_array($moveChapters)) {
    $answer['moveChapter'][] = ['nameChapter' => htmlspecialchars_decode($moveChapter[1], ENT_QUOTES), 'idStart' => $moveChapter[2]];
}
echo json_encode($answer);
