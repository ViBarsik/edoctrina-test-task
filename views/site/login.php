<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $User app\models\user\User */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Sing in to LogicalQuiz';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-10\">{input}</div>\n<div class=\"col-lg-offset-2 col-lg-10\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>

    <?= $form->field($User, 'email')->textInput(['autofocus' => true]) ?>
    <?= $form->field($User, 'password')->passwordInput() ?>
    <div class="form-group text-right">
        <div class="col-lg-12">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
