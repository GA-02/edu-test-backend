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
$labs = mysqli_query($link, "SELECT `labs`.`IDLab`, `labs`.`StartNumber`, `labs`.`EndNumber`, `labs`.`Theme`, `common_statuses`.`Name` FROM `labs` INNER JOIN `common_statuses` ON `labs`.`IDStatus` = `common_statuses`.`IDStatus` WHERE NOT(`labs`.`IDStatus` = 3) ORDER BY `labs`.`StartNumber`");

while ($lab = mysqli_fetch_array($labs)) {
    
    if($lab[1] == $lab[2]){
        $numberLab = $lab[1];
    }
    else{
        $numberLab = $lab[1]. ' - ' . $lab[2];
    }
    $answer[] = ['idLab' => $lab[0], 'numberLab' => $numberLab, 'theme' => htmlspecialchars_decode($lab[3], ENT_QUOTES), 'status' => $lab[4]];
}
echo json_encode($answer);
