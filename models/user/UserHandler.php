<?php
namespace app\models\user;

use app\models\user\UserIdentity;
use Yii;
use app\models\ModelHandler;


/**@property User $User*/
class UserHandler extends ModelHandler
{
    private $User;

    public function init() {
        $this->User = new User();
    }

    public function login(){
        $this->User->attributes = Yii::$app->request->post('User');
        if (!$this->User->validate()) {
            return false;
        }
        return Yii::$app->user->login((new UserIdentity($this->User->attributes)), 0);
    }

    public function registration() {
        $this->User->attributes = Yii::$app->request->post('User');
        if (!$this->User->validate()) {
            return false;
        }

        return $this->User->create();
    }

    public function getUser($scenario = 'default'){
        $this->User->scenario = $scenario;
        return $this->User;
    }
}