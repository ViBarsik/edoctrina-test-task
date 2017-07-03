<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $Quiz app\models\quiz\Quiz */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Create New Quiz';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('/css/views/quiz/create.css');
$this->registerJsFile('/js/views/quiz/create.js', ['depends' => [ 'yii\web\JqueryAsset' ] ]);
?>

<div class="quiz-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr>
    <?php $form = ActiveForm::begin([
        'id' => 'quiz-create-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-10\">{input}</div>\n<div class=\"col-lg-offset-2 col-lg-10\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>

    <?= $form->field($Quiz, 'quiz')->textInput(['autofocus' => true]) ?>
    <?= $form->field($Quiz, 'questions_count')->textInput() ?>

    <div class="form-group text-right">
        <div class="col-lg-12">
            <?= Html::submitButton('Generate New Quiz', ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>