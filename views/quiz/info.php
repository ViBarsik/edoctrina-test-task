<?php
/* @var $this yii\web\View */
/* @var $Quiz app\models\quiz\Quiz */
use yii\helpers\Html;

$quizQuestions = $Quiz->getQuizQuestions();
$answers = $Quiz->getQuizQuestionsAnswers();
$userAnswers = $Quiz->getUserTest() ? json_decode($Quiz->getUserTest()->answers,true) : [];

foreach ($answers as $key=>$answer){
    if(isset($quizQuestions[$answer['question_id']])){
        $quizQuestions[$answer['question_id']] ['answers'] [$key] = $answer['answer'];
    }
}

foreach($quizQuestions as $kq=>&$question){
    if(isset($userAnswers[$kq])){
        $question['selectedAnswer'] = $userAnswers[$kq];
        foreach($question['answers'] as $ka=>$answer){
            if($answers[$ka]['is_true']){
                $question['trueAnswer'] = $ka;
                break;
            }
        }
    } else {
        $question['selectedAnswer'] = 0;
        $question['trueAnswer'] = 0;
    }
}

$this->title = "Information about the quiz '{$Quiz->quiz}'";
$this->registerCssFile('/css/views/quiz/test.css');
$this->registerJsFile('/js/views/quiz/test.js', ['depends' => [ 'yii\web\JqueryAsset' ] ]);
?>


<div class="quiz-test text-center">
    <h1 class=""><?= Html::encode($this->title) ?></h1>
    <canvas id="quiz-test-questions" width="375"></canvas>
</div>

<script>
    var QuizQuestions = <?=json_encode($quizQuestions)?>;
</script>