<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $quizList array */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Quiz list';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('/css/views/quiz/index.css');
$this->registerJsFile('/js/views/quiz/index.js', ['depends' => [ 'yii\web\JqueryAsset' ] ]);
?>
<div class="quiz-list">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php foreach ($quizList as $quiz) :
        $isTest = isset($userTestList[$quiz['quiz_id']]);
        if($isTest){
            $test = $userTestList[$quiz['quiz_id']];
        }
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <? if ($isTest) : ?>
                <div class="quiz-list-item <?=$test['status'] ? 'taked' : 'testing' ;?>">
                    <h2><?=$quiz['quiz']?></h2>
                    <p>Count questions: <?=$quiz['questions_count']?></p>
                    <p>True answers: <?=
                        $test['score_true_answers'] . '/' . $quiz['questions_count'] . ' (' .
                        (round(($test['score_true_answers'] * 100 / $quiz['questions_count']),1)) . '%)'
                        ?>
                    </p>
                    <p>Start Date: <?=date('H:i:s d-m-Y',$test['create_time'])?></p>
                    <p><?=$test['status'] ? 'Finish Date: ' . date('H:i:s d-m-Y',$test['update_time']): '.' ;?></p>
                    <p>Author: <?=(int)$quiz['user_id'] ? $quiz['login'] : "UFO";?></p>
                    <p>Create Date: <?=date('H:i:s d-m-Y',$quiz['create_time'])?></p>
                    <div class="text-center"><a class="btn btn-primary" href="/quiz/info/<?=$quiz['quiz_id']?>"><?=$test['status'] ? 'View Info' : 'Continue' ;?></a></div>
                </div>
            <? else: ?>
                <div class="quiz-list-item <?=$isTest ? 'taked' : '';?>">
                    <h2><?=$quiz['quiz']?></h2>
                    <p>Count questions: <?=$quiz['questions_count']?></p>
                    <p>.</p>
                    <p>.</p>
                    <p>.</p>
                    <p>Author: <?=(int)$quiz['user_id'] ? $quiz['login'] : "UFO";?></p>
                    <p>Create Date: <?=date('H:i:s d-m-Y',$quiz['create_time'])?></p>
                    <div class="text-center"><a class="btn btn-success" href="/quiz/test/<?=$quiz['quiz_id']?>">Quiz Started</a></div>
                </div>
            <? endif; ?>
        </div>
    <?php endforeach; ?>

</div>
