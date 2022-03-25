<?php

header("Access-Control-Allow-Origin: *");

$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$lab = mysqli_query($link, "SELECT `StartNumber`, `EndNumber`, `Theme`, `Goal`, `Equipment`, `Content` FROM `labs` WHERE `IDLab` = " . $_POST['idLab']);
$lab = mysqli_fetch_array($lab);
$answer['startNumber'] = $lab[0];
$answer['endNumber'] = $lab[1];
$answer['theme'] = htmlspecialchars_decode($lab[2], ENT_QUOTES);
$answer['goal'] = htmlspecialchars_decode($lab[3], ENT_QUOTES);
$answer['equipment'] = htmlspecialchars_decode($lab[4], ENT_QUOTES);
$answer['content'] = htmlspecialchars_decode($lab[5], ENT_QUOTES);

$nextLab = mysqli_query($link, "SELECT `IDLab` FROM `labs` INNER JOIN (SELECT MIN(`StartNumber`) AS 'Start' FROM labs WHERE `StartNumber`>" . $answer['endNumber'] . " AND (`IDStatus` = 2)) AS `request1` ON `labs`.`StartNumber` = `request1`.`Start` WHERE `IDStatus` = 2");
$nextLab = mysqli_fetch_array($nextLab);
if ($nextLab)
    $answer['idNextLab'] = $nextLab[0];

$prevLab = mysqli_query($link, "SELECT `IDLab` FROM `labs` INNER JOIN (SELECT MAX(`EndNumber`) AS 'End' FROM labs WHERE `EndNumber`<" . $answer['startNumber'] . " AND (`IDStatus` = 2)) AS `request1` ON `labs`.`EndNumber` = `request1`.`End` WHERE `IDStatus` = 2");
$prevLab = mysqli_fetch_array($prevLab);
if ($prevLab)
    $answer['idPrevLab'] = $prevLab[0];

echo json_encode($answer);
