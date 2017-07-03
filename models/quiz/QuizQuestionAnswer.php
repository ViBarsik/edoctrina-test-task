<?php
namespace app\models\quiz;

use Yii;
use app\models\Model;
use app\models\traits;

class QuizQuestionAnswer extends Model
{
    use traits\TQuiz;
    use traits\TQuizQuestion;

    public $answer_id = 0;
    public $question_id = 0;
    public $quiz_id = 0;
    public $answer;
    public $is_true;
    
    static function tableName() {
        return "quiz_question_answer";
    }

    public function scenarios() {
        return [
            'default'   => ['answer_id','question_id','quiz_id','answer','is_true'],
            'create'    => ['question_id','quiz_id','answer','is_true']
        ];
    }

    public function rules() {
        return [
            [['answer_id','quiz_id','question_id'], 'integer'],
            [['quiz_id','question_id'], 'integer', 'min'=>1],
            [['is_true'], 'boolean'],
            ['answer', 'string'],
        ];
    }

    public function create() : int {
        $result = (int)Yii::$app->db->createCommand()->insert(self::tableName(), $this->getAttributes())->execute();
        return $result;
    }

    public function generateAnswer(string &$answers, int $isNumber){
        if($isNumber){
            $this->answer = random_int(1,9);
            $flag = strpos($answers,(string)$this->answer) === false;
        } else {
            $this->answer = strtoupper(Yii::$app->security->generateRandomString(1));
            $flag = (!is_numeric($this->answer) && strpos('-_'.$answers,$this->answer) === false);
        }

        if($flag){
            $answers .= $this->answer;
        } else {
            $this->generateAnswer($answers,$isNumber);
        }
    }

    public function findFromQuiz() : array {
        return Yii::$app->db->createCommand(
            "SELECT * FROM ". self::tableName() . " WHERE quiz_id=:quiz_id", [':quiz_id'=>$this->quiz_id]
        )->queryAll() ?: [];
    }

    public function findFromQuestion() : array {
        return Yii::$app->db->createCommand(
            "SELECT * FROM ". self::tableName() . " WHERE question_id=:question_id", [':question_id'=>$this->question_id]
        )->queryAll() ?: [];
    }

    public function findById() {
        $attributes = Yii::$app->db->createCommand("SELECT * FROM ". self::tableName() . " WHERE answer_id=:answer_id", [':answer_id'=>$this->answer_id])->queryOne();

        if(!$attributes) {
            return null;
        }

        $this->attributes = $attributes;

        return $this;
    }
}