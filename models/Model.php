<?php
namespace app\models;

use Yii;

class Model extends \yii\base\Model
{
    public function getAttributes($names = null, $except = []) {
        $scenario = $this->scenario;
        $names = $this->scenarios()[$scenario] ?? null;
        return parent::getAttributes($names, $except);
    }

    public function findByCondition(array $fields) {
        $params = [];
        $where = '';

        foreach ($fields as $key=>$field){
            if(isset($this->attributes[$field])){
                $where .= $where ? " AND {$field}=:{$field}" : "{$field}=:{$field}";
                $params[':'.$field] = $this->$field;
            }
        }

        if(!$params){
            return null;
        }

        $attributes = Yii::$app->db->createCommand(
            "SELECT * 
                FROM ". static::tableName() . " 
                WHERE {$where}", $params
        )->queryOne();

        if(!$attributes) {
            return null;
        }

        $this->attributes = $attributes;

        return $this;
    }
}