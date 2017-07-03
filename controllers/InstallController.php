<?php
namespace app\controllers;

use Yii;
use app\models\install\Generator;


class InstallController extends \app\controllers\Controller
{
    public $layout = 'install';

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionRun() {
        if(isset(Yii::$app->params['isInstall'])){
            return $this->redirect('/');
        }
        try{
            Generator::createTables();
            Generator::updateConfig();
            return $this->refresh();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}