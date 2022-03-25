<?php

header("Access-Control-Allow-Origin: *");
$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$result = mysqli_query($link, "SELECT * FROM users Where `Email` = '" . $_POST['email'] . "' AND (`IDRole` = 1 OR `IDRole` = 2)");

if ($result->num_rows == 0) {
	$answer['error'] = 'На этот почтовый адрес не зарегистрирован ни один аккаунт';
	echo json_encode($answer);
	exit;
}
$userPassword = mysqli_query($link, "SELECT `Password` FROM `users` WHERE `Email` = '" . $_POST['email']. "'");
$userPassword = mysqli_fetch_array($userPassword)[0];

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
$result =  $mailSMTP->send($to, 'Восстановление пароля', 'Ваш текущий пароль: '. $userPassword, $from);
// $result =  $mailSMTP->send('Кому письмо', 'Тема письма', 'Текст письма', 'Отправитель письма');

if ($result === false) {
	$answer['error'] = 'Ошибка отправки сообщения на почту';
	echo json_encode($answer);
	exit;
}

$answer['result'] = 'Ok';
echo json_encode($answer);
