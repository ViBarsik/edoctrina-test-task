<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
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
    <?= Html::csrfMetaTags() ?>
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

    $items = [
        ['label' => 'Quiz List', 'url' => ['/quiz/']]
    ];
    if(Yii::$app->user->isGuest){
        $items[] =  ['label' => 'Sign In', 'url' => ['/login']];
        $items[] =  ['label' => 'Sign Up', 'url' => ['/registration']];
    } else {
        $items[] =  '<li>'
            . Html::beginForm(['/logout'], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->login . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
        . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $items,
    ]);

    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
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
    $this->registerJsFile('/js/site.js', ['depends' => [ 'yii\web\JqueryAsset' ] ]);
    $this->endBody();

?>

<script>
    $(document). ready(function() {

        <?php if(isset($this->context->socketAuthLink) && $this->context->socketAuthLink) : ?>
            try{
                socket = io('<?=$this->context->socketAuthLink;?>');
                socket.emit('open', 'ping');
                socket.on('open', function(message){
                    console.log(message);
                });

                socket.on('selectAnswer', function(data){
                    Cnvs.socketSelect(data);
                });

                socket.on('reload', function(message){
                    console.log(message);
                    location = '/quiz';
                });

            } catch (e){
                console.log("Socket is off on this page!");
            }
        <?php endif ?>

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
