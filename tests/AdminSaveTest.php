<?php

header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Origin: *");
$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$requestParams = json_decode(file_get_contents('php://input'), true);
$test = $requestParams['test'];
$idTest = $requestParams['idTest'];
mysqli_query($link, "UPDATE `tests` SET `Name`='" . htmlspecialchars($test['nameTest'], ENT_QUOTES) . "', `IDComplexity`=" . $test['idComplexity'] . ",`IDStatus`=" . $test['idStatus'] . " WHERE `IDTest` = " . $idTest);
mysqli_query($link, "DELETE FROM `questions` WHERE `IDTest` = " . $idTest);
foreach ($test['questions'] as $question) {
    mysqli_query($link, "INSERT INTO `questions`(`IDQuestion`, `Name`, `Code`, `IDType`, `IDTest`) VALUES (Null, '" . htmlspecialchars($question['nameQuestion'], ENT_QUOTES) . "', '" . htmlspecialchars($question['codeQuestion'], ENT_QUOTES) . "', " . $question['idType'] . ", " . $idTest . ")");
    $idQuestion = mysqli_insert_id($link);
    foreach ($question['answers'] as $answer) {
        mysqli_query($link, "INSERT INTO `answers`(`IDAnswer`, `IDQuestion`, `Name`, `Code`, `IsTrue`) VALUES (NULL, " . $idQuestion . ", '" . htmlspecialchars($answer['nameAnswer'], ENT_QUOTES) . "', '" . htmlspecialchars($answer['codeAnswer'], ENT_QUOTES) . "', " . $answer['isTrue'] . ")");
    }
   
    foreach ($question['lectures'] as $lecture) {
        mysqli_query($link, "INSERT INTO `questions_lectures`(`IDString`, `IDQuestion`, `IDLecture`) VALUES (NULL, " . $idQuestion . ", " . $lecture['idLecture'] . ")");
    }
}

echo "Тест сохранен успешно";
