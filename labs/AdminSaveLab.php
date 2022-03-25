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
$idLab = $_POST['idLab'];
if ($_POST['idStatus'] == 2) {
    $labs = mysqli_query($link, "SELECT `IDLab`, `StartNumber`, `EndNumber` FROM `labs` WHERE `IDStatus` = 2 AND NOT(`IDLab` = " . $idLab . ") ORDER BY `StartNumber`");

    while ($lab = mysqli_fetch_array($labs)) {
        $numbersLabs[] = ['startNumber' => $lab[1], 'endNumber' => $lab[2]];
    }
    for ($i = $_POST['startNumber']; $i <= $_POST['endNumber']; $i++) {
        foreach ($numbersLabs as $item) {
            if ($item['startNumber'] <= $i && $item['endNumber'] >= $i) {
                echo 'Лабораторная работа с таким номером (' . $i . ') уже существует';
                exit;
            }
        }
    }
}
mysqli_query($link, "UPDATE `labs` SET `StartNumber`=" . $_POST['startNumber'] . ", `EndNumber`=" . $_POST['endNumber'] . ", `Theme`= '" . htmlspecialchars($_POST['theme'], ENT_QUOTES) . "', `Goal`='" . htmlspecialchars($_POST['goal'], ENT_QUOTES) . "', `Equipment`='" . htmlspecialchars($_POST['equipment'], ENT_QUOTES) . "', `Content`='" . htmlspecialchars($_POST['content'], ENT_QUOTES) . "', `IDStatus`=" . $_POST['idStatus'] . " WHERE `IDLab` = " . $idLab);
echo "Лабораторная работа успешно сохранена";
