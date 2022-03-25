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
$tests = mysqli_query($link, "SELECT `tests`.`IDTest`, `tests`.`Name`, `test_complexity`.`IDСomplexity`, IFNULL(COUNT(`questions`.`IDTest`), 0) AS `Количество вопросов`, `test_statuses`.`Name`  FROM `tests` INNER JOIN `test_complexity` ON `tests`.`IDComplexity` = `test_complexity`.`IDСomplexity` LEFT JOIN `questions` ON `questions`.`IDTest` = `tests`.`IDTest` INNER JOIN `test_statuses` ON `tests`.`IDStatus` = `test_statuses`.`IDStatus` WHERE NOT(`tests`.`IDStatus` = 3) GROUP BY `tests`.`IDTest`, `tests`.`Name`, `test_complexity`.`Name`");

while ($test = mysqli_fetch_array($tests)) {
    
    $answer[] = ['idTest' => $test[0], 'nameTest' => htmlspecialchars_decode($test[1], ENT_QUOTES), 'idComplexity' => $test[2], 'countQuestion' => $test[3], 'status' => $test[4]];
}
echo json_encode($answer);
