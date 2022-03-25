<?php

header("Access-Control-Allow-Origin: *");
$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$tests = mysqli_query($link, "SELECT `tests`.`IDTest`, `tests`.`Name`, `test_complexity`.`Name`, COUNT(`questions`.`IDTest`) AS `Количество вопросов` FROM `tests` INNER JOIN `test_complexity` ON `tests`.`IDComplexity` = `test_complexity`.`IDСomplexity` INNER JOIN `questions` ON `questions`.`IDTest` = `tests`.`IDTest` WHERE `tests`.`IDStatus` = 2 GROUP BY `tests`.`IDTest`, `tests`.`Name`, `test_complexity`.`Name` ORDER BY `tests`.`IDComplexity`");

while ($test = mysqli_fetch_array($tests)) {
    
    $answer[] = ['idTest' => $test[0], 'nameTest' => htmlspecialchars_decode($test[1], ENT_QUOTES), 'complexity' => $test[2], 'countQuestion' => $test[3]];
}
echo json_encode($answer);

