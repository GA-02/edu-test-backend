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
$lectures = mysqli_query($link, "SELECT `lectures`.`IDLecture`, `lectures`.`Name`, `chapters`.`Name`, `lectures`.`SerialNumber`, `lectures`.`IDStatus` FROM `lectures` LEFT JOIN `chapters` ON `lectures`.`IDChapter` = `chapters`.`IDChapter` WHERE not(`lectures`.`IDStatus` = 3) ORDER BY `lectures`.`IDChapter`, `lectures`.`SerialNumber`");

while ($lecture = mysqli_fetch_array($lectures)) {
    
    $answer[] = ['idLecture' => $lecture[0], 'nameLecture' => htmlspecialchars_decode($lecture[1], ENT_QUOTES), 'nameChapter' => htmlspecialchars_decode($lecture[2], ENT_QUOTES), 'serialNumber' => $lecture[3], 'idStatus' => $lecture[4]];
}
echo json_encode($answer);
