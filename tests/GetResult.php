<?php

header("Access-Control-Allow-Origin: *");
$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$idResult = $_POST['idResult'];


$test = mysqli_query($link, "SELECT `tests`.`Name` FROM `test_results` INNER JOIN `tests` ON `test_results`.`IDTest` = `tests`.`IDTest` WHERE `test_results`.`IDResult` = " . $idResult);
$testName = mysqli_fetch_array($test)[0];
$answer['testName'] = htmlspecialchars_decode($testName, ENT_QUOTES);
$user = mysqli_query($link, "SELECT `users`.`Name` FROM `test_results` INNER JOIN `users` ON `test_results`.`IDUser` = `users`.`IDUser` WHERE `test_results`.`IDResult` = " . $idResult);
$userName = mysqli_fetch_array($user)[0];
$answer['userName'] = htmlspecialchars_decode($userName, ENT_QUOTES);
$date = mysqli_query($link, "SELECT `Date`, `Time` FROM `test_results` WHERE `IDResult` = " . $idResult);
$date = mysqli_fetch_array($date);
$answer['date'] = date("d.m.Y", strtotime($date[0])) . ' (' . $date[1]. ')';
$result = mysqli_query($link, "SELECT  `test_results`.`Score`, COUNT(`questions`.`IDTest`) AS 'maxScore' FROM `test_results` INNER JOIN `tests` ON `test_results`.`IDTest` = `tests`.`IDTest` INNER JOIN `questions` ON `questions`.`IDTest` = `tests`.`IDTest` WHERE `test_results`.`IDResult` = " . $idResult . " GROUP BY `test_results`.`Score`");
$resultScores = mysqli_fetch_array($result);
$answer['scoreResult'] = $resultScores[0];
$answer['scoreMax'] = $resultScores[1];

$recommendedLectures = mysqli_query($link, "SELECT `lectures`.`IDLecture`, `chapters`.`Name`, `lectures`.`Name` FROM `lecture_recommended` INNER JOIN `lectures` ON `lecture_recommended`.`IDLecture` = `lectures`.`IDLecture` INNER JOIN `chapters` ON `chapters`.`IDChapter` = `lectures`.`IDChapter` WHERE `lecture_recommended`.`IDResult` = " . $idResult);

while ($recommendedLecture = mysqli_fetch_array($recommendedLectures)) {

    $answerLectures[] = ['idLecture' => $recommendedLecture[0], 'nameChapter' => htmlspecialchars_decode($recommendedLecture[1], ENT_QUOTES), 'nameLecture' => htmlspecialchars_decode($recommendedLecture[2], ENT_QUOTES)];
}
if (isset($answerLectures))
    $answer['recommendedLectures'] = $answerLectures;


echo json_encode($answer);



