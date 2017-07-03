<?php

/* @var $this yii\web\View */

$this->title = 'Install LogicalQuiz';
?>

<div class="site-index">
    <div class="jumbotron">
        <h1>Welcome to the Install LogicalQuiz!</h1>
        <p class="text-left">Please read file "<?=Yii::$aliases['@app'].'/INSTALL.MD'?>":</p>
        <p class="text-left">Please open file "<?=Yii::$aliases['@app'].'/config/params.php'?>" and correct next params:</p>

        <p class="text-left"><strong>[socket_url]</strong> =>  NodeServer URL next format: 'http://example.com:3745'. INSTALL.MD 1.2</p>
        <p class="text-left"><strong>[mysql_host]</strong> =>  Database location. INSTALL.MD 1.3</p>
        <p class="text-left"><strong>[mysql_password]</strong> => The database password created in the console. INSTALL.MD 1.3</p>
        <p class="text-left"><strong>[mysql_user]</strong> => The database user created in the console. INSTALL.MD 1.3</p>
        <p class="text-left"><strong>[mysql_database]</strong> => The name of the database that was created in the console. INSTALL.MD 1.3</p>
        <br>
        <p class="lead">After correcting click this button</p>
        <p>
            <a class="btn btn-lg btn-success" href="/install/run">Install LogicalQuiz</a>
        </p>
    </div>
</div>


