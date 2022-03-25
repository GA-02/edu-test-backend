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
if (htmlspecialchars_decode($account['Password'], ENT_QUOTES) != $_POST['password']) {
    ReturnError('Пароль неправильный');
}

if ($account['IDRole'] == 3) {
    ReturnError('Аккаунт был заблокирован');
}

if ($account['IDRole'] == 4) {
    ReturnError('Для входа в аккаунт необходимо подтвердить почтовый адрес');
}

$answer['name'] = htmlspecialchars_decode($account['Name'], ENT_QUOTES);
$answer['email'] = $account['Email'];
$answer['idRole'] = $account['IDRole'];

echo json_encode($answer);


