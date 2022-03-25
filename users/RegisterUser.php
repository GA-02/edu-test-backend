<?php

function gen_password($length = 6)
{
	$password = '';
	$arr = array(
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
		'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
		'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
		'1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
	);

	for ($i = 0; $i < $length; $i++) {
		$password .= $arr[random_int(0, count($arr) - 1)];
	}
	return $password;
}

header("Access-Control-Allow-Origin: *");
$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$result = mysqli_query($link, "SELECT * FROM users Where Email = '" . $_POST['email'] . "'");

if ($result->num_rows != 0) {
	$answer['error'] = 'На этот почтовый адрес уже зарегистрирован аккаунт';
	echo json_encode($answer);
	exit;
}
$keyForConfirmRegister = gen_password(15);
mysqli_query($link, "INSERT INTO `users`(`IDUser`, `Name`, `Email`, `Password`, `IDRole`, `SecretKey`) VALUES (NULL, '" . htmlspecialchars($_POST['name'], ENT_QUOTES) . "', '" . $_POST['email'] . "', '" . htmlspecialchars(trim($_POST['password']), ENT_QUOTES) . "', 4, '" . $keyForConfirmRegister . "')");
$idUser = mysqli_insert_id($link);

require_once "SendMailSmtpClass.php";
$mailSMTP = new SendMailSmtpClass('edu-test@inbox.ru', 'kF6m8unZFgyK5rFQ4GzG', 'ssl://smtp.mail.ru', 465, "utf-8");
// $mailSMTP = new SendMailSmtpClass('логин', 'пароль', 'хост', 'порт', 'кодировка письма');

// от кого
$from = array(
	"Обучающий комплекс по программированию", // Имя отправителя
	"edu-test@inbox.ru" // почта отправителя
);
// кому отправка. Можно указывать несколько получателей через запятую
$to = $_POST['email'];

// отправляем письмо
$result =  $mailSMTP->send($to, 'Регистрация аккаунта', 'Для завершения регистрации аккаунта перейдите по ссылке: http://localhost:3000' . $_POST['frontHost'] . 'profile/confirm?id=' . $idUser . '&key=' . $keyForConfirmRegister, $from);
// $result =  $mailSMTP->send('Кому письмо', 'Тема письма', 'Текст письма', 'Отправитель письма');

if ($result === false) {
	$answer['error'] = 'Ошибка отправки сообщения на почту';
	echo json_encode($answer);
	exit;
}

$answer['result'] = 'Ok';
echo json_encode($answer);
