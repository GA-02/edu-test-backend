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
$tests = mysqli_query($link, "SELECT `test_results`.`IDResult`, `tests`.`Name`, `test_results`.`Score`, COUNT(`questions`.`IDTest`) AS 'Max', `test_results`.`Date`, `test_results`.`Time`, `users`.`Name` FROM `test_results` INNER JOIN `tests` ON `tests`.`IDTest` = `test_results`.`IDTest` INNER JOIN `questions` ON `questions`.`IDTest` = `tests`.`IDTest` INNER JOIN `users` ON `users`.`IDUser` = `test_results`.`IDUser` GROUP BY `test_results`.`IDResult`, `tests`.`Name`, `test_results`.`Score`, `test_results`.`Date`, `test_results`.`Time` ORDER BY `test_results`.`Date` DESC, `test_results`.`Time` DESC");

while ($test = mysqli_fetch_array($tests)) {
    
    $answer[] = ['idResult' => $test[0], 'nameTest' => htmlspecialchars_decode($test[1], ENT_QUOTES), 'score' => $test[2] ." из ". $test[3], 'date' => date("d.m.Y", strtotime($test[4])), 'time' => $test[5],  'user' => htmlspecialchars_decode($test[6], ENT_QUOTES)];
}
echo json_encode($answer);
