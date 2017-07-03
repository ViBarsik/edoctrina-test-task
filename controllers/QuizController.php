<?php
namespace app\controllers;

use Yii;
use app\models\quiz\QuizHandler;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\quiz\QuizUserHistory;

/** 
 * @property \app\models\quiz\QuizHandler $QuizHandler
 */
class QuizController extends \app\controllers\Controller
{
    public $QuizHandler;
    public $socketAuthLink;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function beforeAction($action) {
        $parent = parent::beforeAction($action);

        $this->QuizHandler = new QuizHandler();

        return $parent;
    }

    public function actionIndex() {
        $quizList = $this->QuizHandler->viewList();

        if(!$quizList){
            Yii::$app->session->setFlash('warning', $this->QuizHandler->getError());
            return $this->goHome();
        }
        
        return $this->render('index', [
            'quizList' => $quizList,
            'userTestList' => $this->QuizHandler->getUserTestList()
        ]);
    }

    public function actionCreate() {        
        $Quiz = $this->QuizHandler->getQuiz('create');

        if(Yii::$app->request->isPost && $this->QuizHandler->create()){
            return $this->redirect('/quiz');
        }

        return $this->render('create', ['Quiz' => $Quiz]);
    }

    public function actionInfo($id) {
        if((int)$id < 1){
            Yii::$app->session->setFlash('warninig', "Undefind Quiz identifier");
            return $this->redirect('/quiz');
        }
        
        $Quiz = $this->QuizHandler->info();
        
        if(!$Quiz){
            Yii::$app->session->setFlash('warning', $this->QuizHandler->getError());
            return $this->redirect('/quiz');
        }

        if(!$Quiz->getUserTest() || $Quiz->getUserTest() && (int)$Quiz->getUserTest()->status === (int)QuizUserHistory::STATUS_START) {
            return $this->redirect('/quiz/test/' . $Quiz->quiz_id);
        }
        
        return $this->render('info', ['Quiz' => $this->QuizHandler->info()]);
    }

    public function actionTest($id) {
        if((int)$id < 1){
            Yii::$app->session->setFlash('warninig', "Undefind Quiz identifier");
            return $this->redirect('/quiz');
        }

        if(Yii::$app->request->isPost){
            if($this->QuizHandler->saveTest()){
                return $this->redirect('/quiz');
            }
            Yii::$app->session->setFlash('warning', $this->QuizHandler->getError());
            return $this->refresh();
        }

        $Quiz = $this->QuizHandler->test();

        if(!$Quiz){
            Yii::$app->session->setFlash('warning', $this->QuizHandler->getError());
            return $this->redirect('/quiz');
        }

        if($Quiz->getUserTest() && (int)$Quiz->getUserTest()->status === (int)QuizUserHistory::STATUS_FINISH) {
            return $this->redirect('/quiz/info/' . $Quiz->quiz_id);
        }

        $this->socketAuthLink = Yii::$app->params['socket_url'] . '?token=' . Yii::$app->session->id . '&room=' . Yii::$app->user->id . '-' . $Quiz->quiz_id;

        return $this->render('test',['Quiz' => $Quiz]);
    }

    public function actionSetAnswer() {
        if(!Yii::$app->request->isAjax){
            Yii::$app->session->setFlash('warning', 'Access denied!');
            return $this->goBack();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->data = $this->QuizHandler->setAnswer();
        Yii::$app->response->send();
    }
}