<?php
namespace app\models;

use yii\base\ErrorException;

abstract class ModelHandler
{
    final public function __construct() {
        $this->init();
    }

    protected function init(){

    }

    final public function __call($function, $arguments) {
        throw new ErrorException( 'Method ['. get_called_class() ."::". $function .'()] not found' );
    }

    final public static function __callStatic($function, $arguments) {
        throw new ErrorException( 'Method ['. get_called_class() ."::". $function .'()] not found' );
    }
}