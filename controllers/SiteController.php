<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\controllers\Controller;
use app\models\user\UserHandler;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionRegistration() {
        $UserHandler = new UserHandler();
        $User = $UserHandler->getUser('create');

        if(Yii::$app->request->isPost && $UserHandler->registration()){
            return $this->redirect('/login');
        }

        return $this->render('registration', [
            'User' => $User,
        ]);
    }

    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $UserHandler = new UserHandler();
        $User = $UserHandler->getUser('login');

        if(Yii::$app->request->isPost && $UserHandler->login()){
            return $this->goBack();
        }

        return $this->render('login', ['User' => $User,]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('registration', [
            'model' => $model,
        ]);
    }
}
