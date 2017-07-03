<?php
namespace app\models\traits;

use app\models\user\User;

/**
 * @property User $User
 */
trait TUser
{
    private $User;
    public function getUser(){
        if(!$this->User) {
            $this->User = (new User(['user_id' => $this->user_id]))->findById();
        }

        return $this->User;
    }
}