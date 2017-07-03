<?php

namespace app\models\user;

use Yii;
use app\models\user\User;

class UserIdentity extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $user_id;
    public $login;
    public $email;
    public $password;
    public $create_time;
    public $auth_key;
    public $access_token;

    private static $users = [
        '9999999999' => [
            'user_id' => '9999999999',
            'login' => 'demo',
            'email' => 'demo@example.com',
            'password' => 'demodemo',
            'auth_key' => '9999999999999999',
            'access_token' => '99999999999999999999999999999999',
        ],
    ];

    public static function findIdentity($id) {
        $User = (new User(['user_id' => $id]))->findById();

        return $User ? new static($User->attributes) : null;
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        $User = (new User(['access_token' => $token]))->findByAccessToken();

        return $User ? new static($User->attributes) : null;
    }

    public static function findByEmail($email) {
        $User = (new User(['email' => $email]))->findByEmail();

        return $User ? new static($User->attributes) : null;
    }

    public function getId() {
        return $this->user_id;
    }

    public function getAuthKey() {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey) {
        return $this->auth_key === $authKey;
    }

    public function validatePassword($password) {
        return $this->password === $password;
    }
}
