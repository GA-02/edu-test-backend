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

$result = mysqli_query($link, "SELECT * FROM users Where `Email` = '" . $_POST['email'] . "'");

if ($result->num_rows == 0) {
    ReturnError('На этот почтовый адрес не зарегистрирован ни один аккаунт');
}

$account = mysqli_fetch_array($result);
if (htmlspecialchars_decode($account['Password'], ENT_QUOTES) != $_POST['oldPassword']) {
    ReturnError('Пароль неправильный');
}


$result = mysqli_query($link, "UPDATE `users` SET `Password`='".htmlspecialchars($_POST['newPassword'], ENT_QUOTES)."' WHERE `IDUser`=" . $account['IDUser']);

echo json_encode([]);

