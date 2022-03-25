<?php

header("Access-Control-Allow-Origin: *");
$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$idResult = $_POST['idResult'];

$idTestRequest = mysqli_query($link, "SELECT `IDTest` FROM `test_results` WHERE `IDResult` = " . $idResult);
$idTest = mysqli_fetch_array($idTestRequest)[0];

$maxScore = mysqli_query($link, "SELECT COUNT(`IDQuestion`) FROM `questions` WHERE `IDTest` = " . $idTest);
$maxScore = mysqli_fetch_array($maxScore)[0];
$results = mysqli_query($link, "SELECT `Score`, COUNT(`IDTest`) AS 'Кол' FROM `test_results` WHERE `IDTest`= " . $idTest . " AND `Score`<=" . $maxScore . " GROUP BY `Score` ORDER BY `Score`");

while ($result = mysqli_fetch_array($results)) {
    $answer[$result[0]] = $result[1];
}
if (!isset($answer[$maxScore])) {
    $answer[$maxScore] = 0;
}
echo json_encode($answer);
