<?php

function ReturnError($errorDescription)
{
    $answer['error'] = $errorDescription;
    echo json_encode($answer);
    exit;
}

header("Access-Control-Allow-Origin: *");
$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$result = mysqli_query($link, "SELECT `IDUser`, `Email`, `Password` FROM `users` WHERE `Email` = '" . $_POST['email'] . "' AND NOT(`IDRole` = 3)");

if ($result->num_rows == 0) {
    ReturnError('На этот почтовый адрес не зарегистрирован ни один аккаунт');
}

$account = mysqli_fetch_array($result);

if (htmlspecialchars_decode($account['Password'], ENT_QUOTES) != $_POST['password']) {
    ReturnError('Пароль неправильный');
}

$result = mysqli_query($link, "SELECT `test_results`.`IDResult`, `tests`.`Name`, `test_results`.`Score`, COUNT(`questions`.`IDTest`) AS 'Max', `test_results`.`Date`, `test_results`.`Time` FROM `test_results`  INNER JOIN `tests` ON `tests`.`IDTest` = `test_results`.`IDTest` INNER JOIN `questions` ON `questions`.`IDTest` = `tests`.`IDTest` WHERE `test_results`.`IDUser` = " . $account['IDUser'] . " GROUP BY `test_results`.`IDResult`, `tests`.`Name`, `test_results`.`Score`, `test_results`.`Date`, `test_results`.`Time` ORDER BY `test_results`.`Date` DESC, `test_results`.`Time` DESC");

while ($resultUser = mysqli_fetch_array($result)) {
    $answer[] = ['idResult' => $resultUser[0], 'testName' => htmlspecialchars_decode($resultUser[1], ENT_QUOTES), 'resultScore' => $resultUser[2] . ' из ' . $resultUser[3], 'date' => date("d.m.Y", strtotime($resultUser[4])), 'time' => $resultUser[5]];
}
if(!isset($answer)){
    $answer = [];
}
echo json_encode($answer);


