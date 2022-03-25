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
$idTest = $_POST['idTest'];
// echo time();
// $idTest = 1;
$testInfo = mysqli_query($link, "SELECT `tests`.`Name`, `tests`.`IDComplexity`, `tests`.`IDStatus` FROM `tests` WHERE `IDTest` = " . $idTest);
$testInfo = mysqli_fetch_array($testInfo);
$answer['nameTest'] = htmlspecialchars_decode($testInfo[0], ENT_QUOTES);
$answer['idComplexity'] = $testInfo[1];
$answer['idStatus'] = $testInfo[2];

$questions = mysqli_query($link, "SELECT `IDQuestion`, `Name`, `Code`, `IDType` FROM `questions` WHERE `IDTest` = " . $idTest);

while ($question = mysqli_fetch_array($questions)) {

    $idQuestion = $question[0];

    $answersOnQuestionRequest = mysqli_query($link, "SELECT `IDAnswer`, `Name`, `Code`, `IsTrue` FROM `answers` WHERE `IDQuestion` = " . $idQuestion);

    while ($answerOnQuestion = mysqli_fetch_array($answersOnQuestionRequest)) {
        $answersOnQuestion[] = ['idAnswer' => $answerOnQuestion[0], 'nameAnswer' => htmlspecialchars_decode($answerOnQuestion[1], ENT_QUOTES), 'codeAnswer' => htmlspecialchars_decode($answerOnQuestion[2], ENT_QUOTES), 'isTrue' => $answerOnQuestion[3]];
    }

    $lecturesOnQuestionRequest = mysqli_query($link, "SELECT `questions_lectures`.`IDString`, `questions_lectures`.`IDLecture`, `chapters`.`IDChapter` FROM `questions_lectures` INNER JOIN `lectures` ON `questions_lectures`.`IDLecture` = `lectures`.`IDLecture` INNER JOIN `chapters` ON `lectures`.`IDChapter` = `chapters`.`IDChapter` WHERE `IDQuestion` = " . $idQuestion);

    while ($lectureOnQuestion = mysqli_fetch_array($lecturesOnQuestionRequest)) {
        $lecturesOnQuestion[] = ['idString' => $lectureOnQuestion[0], 'idLecture' => $lectureOnQuestion[1], 'idChapter' => $lectureOnQuestion[2]];
    }

    $answerQuestions[] = ['idQuestion' => $question[0], 'nameQuestion' => htmlspecialchars_decode($question[1], ENT_QUOTES), 'codeQuestion' => htmlspecialchars_decode($question[2], ENT_QUOTES), 'idType' => $question[3], 'answers' => $answersOnQuestion ?? [], 'lectures' => $lecturesOnQuestion ?? []];
    unset($answersOnQuestion);
    unset($lecturesOnQuestion);
}
if(!isset($answerQuestions)){
    $answerQuestions = [];
}
$answer['questions'] = $answerQuestions;



$chapters = mysqli_query($link, "SELECT `IDChapter`, `Name` FROM `chapters` ORDER BY `SerialNumber`");
while ($chapter = mysqli_fetch_array($chapters)) {
    
    $lecturesChapter = mysqli_query($link, "SELECT `IDLecture`, `Name` FROM `lectures` WHERE `IDChapter` = " . $chapter[0] . " AND NOT(`IDStatus` = 3)");
    while ($lecture = mysqli_fetch_array($lecturesChapter)) {
        $lectures[] = ['idLecture' => $lecture[0], 'nameLecture' => htmlspecialchars_decode($lecture[1], ENT_QUOTES)];
    }
    if (isset($lectures)){
        $answerChapters[] = ['idChapter' => $chapter[0], 'nameChapter' => htmlspecialchars_decode($chapter[1], ENT_QUOTES), 'lectures' => $lectures];
        unset($lectures);
    }
}
$answer['allChapters'] = $answerChapters;
echo json_encode($answer);
