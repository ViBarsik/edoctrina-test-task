<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $User app\models\user\User */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Join LogicalQuiz';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-registration">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'registration-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-10\">{input}</div>\n<div class=\"col-lg-offset-2 col-lg-10\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>

    <?= $form->field($User, 'login')->textInput(['autofocus' => true]) ?>
    <?= $form->field($User, 'email')->textInput() ?>
    <?= $form->field($User, 'password')->passwordInput() ?>

    <div class="form-group text-right">
        <div class="col-lg-12">
            <?= Html::submitButton('Create an account', ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
