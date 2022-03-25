<?php

header("Access-Control-Allow-Origin: *");
$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);
$result = mysqli_query($link, "SELECT `SecretKey` FROM `users` WHERE `IDUser` = " . $_POST['idUser']);

if ($result->num_rows == 0) {
	$answer['error'] = 'Аккаунта с таким id не существует';
	echo json_encode($answer);
	exit;
}

$secretKey = mysqli_fetch_array($result)[0];
if ($secretKey == '') {
	$answer['error'] = 'Подтверждение аккаунта не требуется';
	echo json_encode($answer);
	exit;
}
if ($secretKey != $_POST['key']) {
	$answer['error'] = 'Ключ не подходит';
	echo json_encode($answer);
	exit;
}

mysqli_query($link, "UPDATE `users` SET `IDRole` = 2, `SecretKey` = '' WHERE `IDUser` = " . $_POST['idUser']);

$answer['result'] = 'Регистрация завершена. Поздравляем!!!';
echo json_encode($answer);
