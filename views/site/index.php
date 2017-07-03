<?php

/* @var $this yii\web\View */

$this->title = 'Logical Quiz';

$this->registerCssFile('/css/views/site/index.css');
$this->registerJsFile('/js/views/site/index.js', ['depends' => [ 'yii\web\JqueryAsset' ] ]);
?>

<canvas id="flying-bubbles"></canvas>

<div class="site-index">
    <div class="jumbotron">
        <h1>Welcome to the logical quiz!</h1>
        <p class="lead">Your can take part in the quiz, and also you can create your own quiz</p>
        <p>
            <a class="btn btn-lg btn-success" href="/quiz/create">Create Quiz</a>
            <a class="btn btn-lg btn-success" href="/quiz">Take Quiz</a>
        </p>
    </div>

</div>


