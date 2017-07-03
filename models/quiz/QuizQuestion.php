<?php
namespace app\models\quiz;

use Yii;
use app\models\Model;
use app\models\traits;

/**
 * @property array $questionAnswers
 */
class QuizQuestion extends Model
{
    use traits\TQuiz;

    const MIN_ANSWER_COUNT = 3;
    const MAX_ANSWER_COUNT = 5;

    public $question_id = 0;
    public $quiz_id = 0;
    public $question;

    private $questionAnswers;

    static function tableName() {
        return "quiz_question";
    }

    public function scenarios() {
        return [
            'default'   => ['question_id','quiz_id','question'],
            'create'    => ['quiz_id','question']
        ];
    }

    public function rules() {
        return [
            [['question_id','quiz_id'], 'integer'],
            ['quiz_id', 'integer', 'min'=>1],
            ['question', 'string'],
        ];
    }

    public function create() : int {
        $result = (int)Yii::$app->db->createCommand()->insert(self::tableName(), $this->getAttributes())->execute();
        $this->question_id = Yii::$app->db->lastInsertID;
        return $result;
    }

    public function findFromQuiz() : array {
        return Yii::$app->db->createCommand(
            "SELECT * FROM ". self::tableName() . " WHERE quiz_id=:quiz_id", [':quiz_id'=>$this->quiz_id]
        )->queryAll() ?: [];
    }

    public function findById() {
        $attributes = Yii::$app->db->createCommand("SELECT * FROM ". self::tableName() . " WHERE question_id=:question_id", [':question_id'=>$this->question_id])->queryOne();

        if(!$attributes) {
            return null;
        }

        $this->attributes = $attributes;

        return $this;
    }

    public function getQuestionAnswers(){
        if(!$this->questionAnswers){
            $this->questionAnswers = (new QuizQuestionAnswer(['question_id' => $this->question_id]))->findFromQuestion();
        }
        
        return $this->questionAnswers;
    }
    

    public function generateQuestion(){
        $this->question = Yii::$app->security->generateRandomString(8);
    }

    public function generateAnswerCount() : int {
        return random_int(self::MIN_ANSWER_COUNT , self::MAX_ANSWER_COUNT );
    }

    public function generateTrueNumberAnswer(int $ansverCount) : int {
        return random_int(1, $ansverCount);
    }

    public function generateAnswersIsNumbers() : int {
        return random_int(0,1);
    }
}