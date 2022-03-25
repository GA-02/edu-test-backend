<?php

header("Access-Control-Allow-Origin: *");
$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);
$userAdmin = mysqli_query($link, "SELECT * FROM users Where `Email` = '" . $_POST['emailAdmin'] . "' AND `Password` = '" . htmlspecialchars($_POST['passwordAdmin'], ENT_QUOTES) . "' AND `IDRole` = 1");
if ($userAdmin->num_rows == 0) {
    echo 'Доступ к данному ресурсу есть только у администратора';
    exit;
}
$idUser = $_POST['idUser'];

mysqli_query($link, "UPDATE `users` SET `Name`='".htmlspecialchars($_POST['name'], ENT_QUOTES)."', `Email`= '".$_POST['email']."', `IDRole`= ".$_POST['idRole']." WHERE  `IDUser` = " . $idUser);
echo "Пользователь успешно сохранен";
