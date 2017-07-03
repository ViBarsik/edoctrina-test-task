<?php

namespace app\models\user;

use Yii;
use app\models\Model;
use yii\db\Command;

class User extends Model
{
    public $user_id;
    public $login;
    public $email;
    public $password;
    public $create_time;
    public $auth_key;
    public $access_token;


    static function tableName() {
        return "user";
    }

    public function scenarios() {
        return [
            'default'   => ['user_id','login','email','password','create_time','auth_key','access_token'],
            'create'    => ['login','email','password','create_time','auth_key','access_token'],
            'login'     => ['email','password']
        ];
    }

    public function rules() {
        return [
            [['login','email','password'],      'required', 'on'=>"create" ],
            [['email', 'password'],             'required', 'on'=>"login"],

            [['login','email'], 'string', 'max' => 100],
            ['email', 'email'],
            ['email', 'validateUniqueEmail', 'on'=>"create"],
            ['password', 'string', 'min'=>8, 'max' => 100],
            ['password', 'validatePassword', 'on'=>"login"],
        ];
    }

    public function create() : int {
        $this->auth_key = Yii::$app->security->generateRandomString(16);
        $this->access_token = Yii::$app->security->generateRandomString(32);
        $this->create_time = time();
        $this->password = Yii::$app->security->generatePasswordHash($this->password);

        $result = (int)Yii::$app->db->createCommand()->insert(self::tableName(), $this->getAttributes())->execute();
        $this->user_id = Yii::$app->db->lastInsertID;

        return $result;
    }

    public function findById() {
        return $this->findUserData('user_id=:user_id', [':user_id'=>$this->user_id]);
    }

    public function findByEmail() {
        return $this->findUserData('email=:email', [':email'=>$this->email]);
    }

    public function findByAccessToken() {
        return $this->findUserData('access_token=:access_token', [':access_token'=>$this->access_token]);
    }

    private function findUserData(string $where, array $params){
        $attributes = Yii::$app->db->createCommand("SELECT * FROM ". self::tableName() . " WHERE {$where}", $params)->queryOne();

        if(!$attributes) {
            return null;
        }

        $this->attributes = $attributes;

        return $this;
    }


    public function validateUniqueEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $User = new User(['email' => $this->$attribute]);

            if ($User->findByEmail()) {
                $this->addError($attribute, 'This email is used.');
            }
        }
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $User = new User(['email' => $this->email]);
            if (!$User->findByEmail()) {
                $this->addError('email', 'Email not found.');
                return null;
            }

            if(!Yii::$app->security->validatePassword($this->$attribute,$User->password)) {
                $this->addError($attribute, 'Incorrect password.');
            }

            $this->scenario = 'default';
            $this->attributes = $User->attributes;
        }
    }

}