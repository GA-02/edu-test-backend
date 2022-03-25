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
$userAnswers = $requestParams['userAnswers'];
$score = 0;
$questions = mysqli_query($link, "SELECT `IDQuestion`, `IDType` FROM `questions` WHERE `IDTest` = " . $requestParams['idTest']);
$wrongAnswersUser = [];
$indexQuestion = 0;
while ($question = mysqli_fetch_array($questions)) {
    $idCurrentQuestion = $question[0];
    switch ($question['IDType']) {
        case 1:
            $answersOnQuestion = mysqli_query($link, "SELECT `IDAnswer`  FROM `answers` WHERE `IDQuestion` =  " . $idCurrentQuestion . " and `isTrue` = true");
            $wrongAnswersUser[$idCurrentQuestion] = false;
            $idCorrectAnswer = mysqli_fetch_array($answersOnQuestion)['IDAnswer'];
            if (isset($userAnswers[$indexQuestion]) && $userAnswers[$indexQuestion] == $idCorrectAnswer)
                $score++;
            else
                $wrongAnswersUser[$idCurrentQuestion] = true;
            break;
        case 2:
            $answersOnQuestion = mysqli_query($link, "SELECT `IDAnswer`, `isTrue`  FROM `answers` WHERE `IDQuestion` =  " . $idCurrentQuestion);
            $wrongAnswersUser[$idCurrentQuestion] = false;
            while ($answerOnQuestion = mysqli_fetch_array($answersOnQuestion)) {
                if ($answerOnQuestion[1] == '1') {
                    if (isset($userAnswers[$indexQuestion]) && in_array($answerOnQuestion[0], $userAnswers[$indexQuestion])) continue;
                } else {
                    if (isset($userAnswers[$indexQuestion]) && !in_array($answerOnQuestion[0], $userAnswers[$indexQuestion])) continue;
                }
                $wrongAnswersUser[$idCurrentQuestion] = true;
                break;
            }
            if (!$wrongAnswersUser[$idCurrentQuestion]) $score++;
            break;
        case 3:
            $answersOnQuestion = mysqli_query($link, "SELECT `Name`  FROM `answers` WHERE `IDQuestion` =  " . $idCurrentQuestion . " and `isTrue` = true");
            $wrongAnswersUser[$idCurrentQuestion] = false;
            $correctAnswer = trim(strtolower(htmlspecialchars_decode(mysqli_fetch_array($answersOnQuestion)['Name'], ENT_QUOTES)));
            if (isset($userAnswers[$indexQuestion]) && trim(strtolower($userAnswers[$indexQuestion])) == $correctAnswer)
                $score++;
            else
                $wrongAnswersUser[$idCurrentQuestion] = true;
            break;
        case 4:
            $answersOnQuestion = mysqli_query($link, "SELECT `Name`  FROM `answers` WHERE `IDQuestion` =  " . $idCurrentQuestion . " and `isTrue` = true");
            $wrongAnswersUser[$idCurrentQuestion] = false;
            $correctAnswer = mysqli_fetch_array($answersOnQuestion)['Name'];
            if (isset($userAnswers[$indexQuestion]) && $userAnswers[$indexQuestion] == $correctAnswer)
                $score++;
            else
                $wrongAnswersUser[$idCurrentQuestion] = true;
            break;
        default:
            break;
    }
    $indexQuestion++;
}


$result = mysqli_query($link, "SELECT `IDUser`, `Password` FROM `users` WHERE `Email` = '" . $requestParams['email'] . "' AND (`IDRole` = 1 OR `IDRole` = 2)");
$idUser = 2;
if ($result->num_rows != 0) {
    $account = mysqli_fetch_array($result);
    if (htmlspecialchars_decode($account['Password'], ENT_QUOTES) == $requestParams['password']) {
        $idUser = $account[0];
    }
}
mysqli_query($link, "INSERT INTO `test_results`(`IDResult`, `IDTest`, `IDUser`, `Score`, `Date`, `Time`) VALUES (NULL, " . $requestParams['idTest'] . ", ".$idUser.", " . $score . ", '" . date('c') . "', '". date("H:i:s") ."')");
$idResult = mysqli_insert_id($link);
foreach ($wrongAnswersUser as $idWrongAnswer => $isWrongAnswer) {
    if ($isWrongAnswer) {
        $lecturesCurrentWrongAnswer = mysqli_query($link, "SELECT `IDLecture` FROM `questions_lectures` WHERE `IDQuestion` = " . $idWrongAnswer);
        while ($lectureCurrentWrongAnswer = mysqli_fetch_array($lecturesCurrentWrongAnswer)) {
            $idRecommendedLecture[] = $lectureCurrentWrongAnswer[0];
        }
    }
}
if (isset($idRecommendedLecture)) {
    $idRecommendedLectures = array_unique($idRecommendedLecture, SORT_NUMERIC);
    foreach ($idRecommendedLectures as $idCurrentLecture) {
        mysqli_query($link, "INSERT INTO `lecture_recommended`(`IDString`, `IDResult`, `IDLecture`) VALUES (NULL, " . $idResult . ", " . $idCurrentLecture . ")");
    }
}


echo ($idResult);
// echo json_encode($answer);
