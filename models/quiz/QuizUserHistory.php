<?php
namespace app\models\quiz;

use Yii;
use app\models\Model;
use app\models\traits\TUser;
use app\models\traits\TQuiz;

/**
 * @property array $quizQuestions
 * @property array $quizQuestionsAnswers
 */
class QuizUserHistory extends Model
{
    use TUser;
    use TQuiz;

    const STATUS_START = 0;
    const STATUS_FINISH = 1;

    public $history_id = 0;
    public $user_id = 0;
    public $quiz_id = 0;
    public $answers = '{}';
    public $status = 0;
    public $score_true_answers = 0;
    public $score_percent = 0;
    public $create_time;
    public $update_time;

    static function tableName() {
        return "quiz_user_history";
    }

    public function scenarios() {
        return [
            'default'   => ['history_id','user_id','quiz_id','answers','status','score_true_answers','score_percent','create_time','update_time'],
            'create'    => ['user_id','quiz_id','answers','status','create_time','update_time'],
            'update'    => ['answers','score_true_answers','update_time'],
            'complete'  => ['status','score_percent','update_time'],
        ];
    }

    public function rules() {
        return [
            [['history_id','user_id','quiz_id'], 'integer'],
            [['quiz_id','question_id'], 'integer', 'min'=>1],
            [['is_true'], 'boolean'],
            ['answer', 'string'],
        ];
    }

    public function create() : int {
        $this->create_time = $this->update_time = time();

        $result = Yii::$app->db->createCommand()->insert(self::tableName(), $this->getAttributes())->execute();

        $this->history_id = Yii::$app->db->lastInsertID;
        return $result;
    }

    public function update(string $condition='', bool $isDefault=true) : int {
        $isDefaultCondition = "history_id = {$this->history_id}";

        if($isDefault) {
            $condition = $condition ? $isDefaultCondition . ' AND ' . $condition : $isDefaultCondition;
        } else {
            $condition = $condition ? $condition : $isDefaultCondition;
        }

        $this->update_time = time();

        return Yii::$app->db->createCommand()->update(
            self::tableName(),
            $this->getAttributes(null,['history_id']),
            $condition
        )->execute();
    }

    public function findByUser(){
        $attributes =  Yii::$app->db->createCommand(
            "SELECT * 
                FROM ". self::tableName() . " 
                WHERE user_id=:user_id AND quiz_id=:quiz_id",
            [':user_id'=>$this->user_id, ':quiz_id'=>$this->quiz_id]
        )->queryAll() ?: [];

        if(!$attributes) {
            return null;
        }

        $this->attributes = $attributes;

        return $this;
    }

    public function findAllByUser() : array {
        return Yii::$app->db->createCommand(
            "SELECT * FROM ". self::tableName() . " WHERE user_id=:user_id", [':user_id'=>$this->user_id]
        )->queryAll() ?: [];
    }
}