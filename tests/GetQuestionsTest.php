<?php

header("Access-Control-Allow-Origin: *");
$settings = explode("\n", file_get_contents('../config.txt'));
$host = trim($settings[0]); //имя хоста, на локальном компьютере это localhost
$user = trim($settings[1]); //имя пользователя
$password = trim($settings[2]); //пароль
$db_name = trim($settings[3]); //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name);

$questions = mysqli_query($link, "SELECT `IDQuestion`, `Name`, `Code`, `IDType` FROM `questions` WHERE `IDTest` = " . $_POST['idTest']);
$indexQuestion = 0;
while ($question = mysqli_fetch_array($questions)) {


    $answersOnQuestion = mysqli_query($link, "SELECT `IDAnswer`, `Name`, `Code` FROM `answers` WHERE `IDQuestion` =  " . $question[0] . "");
    
    while ($answerOnQuestion = mysqli_fetch_array($answersOnQuestion)) {
        $answers[] = ['idAnswer' => $answerOnQuestion[0], 'nameAnswer' => htmlspecialchars_decode($answerOnQuestion[1], ENT_QUOTES), 'codeAnswer' => htmlspecialchars_decode($answerOnQuestion[2], ENT_QUOTES)];
    }
    
    if(isset($answers)){
        shuffle($answers);
        $answer[] = ['idQuestion' => $indexQuestion, 'nameQuestion' => htmlspecialchars_decode($question[1], ENT_QUOTES), 'codeQuestion' =>  htmlspecialchars_decode($question[2], ENT_QUOTES), 'idTypeAnswers' => $question[3], 'answers' => $answers];
        unset($answers);
        $indexQuestion++;
    }
    
}
shuffle($answer);
echo json_encode($answer);
