<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\NavBar;
use app\assets\AppAsset;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Logical Quiz',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= $content ?>
    </div>

</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; IlOk <?= date('Y') ?></p>
        <p class="pull-right">ilativ.oknepilip@gmail.com</p>
    </div>
</footer>

<div class="messageContainer"></div>

<?php
$this->endBody()
?>

<script>
    $(document). ready(function() {
        <?php if(!empty($flashes = Yii::$app->session->getAllFlashes(true))):
            foreach($flashes as $type=>$message): ?>
                MessageBox("<?=$type?>", "<?=$message?>");
            <? endforeach;
            unset($flash_);
        endif; ?>
    });
</script>

</body>

</html>

<?php $this->endPage() ?>
