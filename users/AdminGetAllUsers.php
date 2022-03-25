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
$users = mysqli_query($link, "SELECT `users`.`IDUser`, `users`.`Name`, `users`.`Email`, `user_roles`.`Name` FROM `users` INNER JOIN `user_roles` ON `users`.`IDRole` = `user_roles`.`IDRole` ORDER BY `users`.`IDRole`");

while ($user = mysqli_fetch_array($users)) {
    
    $answer[] = ['idUser' => $user[0], 'nameUser' => htmlspecialchars_decode($user[1], ENT_QUOTES), 'email' => $user[2], 'nameRole' => $user[3]];
}
echo json_encode($answer);
