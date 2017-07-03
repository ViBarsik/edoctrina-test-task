<?php
namespace app\models\quiz;

use Yii;
use app\models\Model;
use app\models\user\User;
use app\models\traits;

/**
 * @property QuizUserHistory $UserTest
 * @property array $quizQuestions
 * @property array $quizQuestionsAnswers
 */
class Quiz extends Model
{
    use traits\TUser;

    const MIN_QUESTIONS_COUNT = 2;
    const MAX_QUESTIONS_COUNT = 30;

    public $quiz_id = 0;
    public $user_id = 0;
    public $quiz;
    public $questions_count;
    public $create_time;
    public $update_time;

    private $UserTest;
    private $quizQuestions;
    private $quizQuestionsAnswers;

    static function tableName() {
        return "quiz";
    }

    public function scenarios() {
        return [
            'default'   => ['quiz_id','user_id','quiz','questions_count','test_count','create_time','update_time'],
            'create'    => ['user_id','quiz','questions_count','create_time','update_time']
        ];
    }

    public function rules() {
        return [
            [['quiz','questions_count'], 'required',   'on'=>"create" ],
            [['quiz_id','user_id','questions_count'], 'integer'],
            ['quiz', 'string', 'min'=>1, 'max' => 100],
            [['questions_count'], 'integer', 'min'=>2, 'max' => 30],
        ];
    }

    public function create() : int {
        $this->user_id = (int)Yii::$app->user->id;
        $this->create_time = $this->update_time = time();

        $result = (int)Yii::$app->db->createCommand()->insert(self::tableName(), $this->getAttributes())->execute();

        $this->quiz_id = Yii::$app->db->lastInsertID;
        return $result;
    }

    public function findAll() : array {
        return Yii::$app->db->createCommand("
            SELECT q.*, u.login
            FROM " . self::tableName() . " AS q 
            LEFT JOIN " . User::tableName() . " AS u ON u.user_id = q.user_id
            ORDER BY q.quiz_id
        ")->queryAll() ?: [];
    }

    public function findById() {
        $attributes = Yii::$app->db->createCommand("SELECT * FROM ". self::tableName() . " WHERE quiz_id=:quiz_id", [':quiz_id'=>$this->quiz_id])->queryOne();

        if(!$attributes) {
            return null;
        }

        $this->attributes = $attributes;

        return $this;
    }

    public function getQuizQuestions(){
        if(!$this->quizQuestions){
            $questions = (new QuizQuestion(['quiz_id' => $this->quiz_id]))->findFromQuiz();
            foreach ($questions as $question){
                $this->quizQuestions[$question['question_id']] = $question;
            }
        }
        
        return $this->quizQuestions;
    }

    public function getQuizQuestionsAnswers(){
        if(!$this->quizQuestionsAnswers){
            $questionsAnswers = (new QuizQuestionAnswer(['quiz_id' => $this->quiz_id]))->findFromQuiz();
            foreach ($questionsAnswers as $answer){
                $this->quizQuestionsAnswers[$answer['answer_id']] = $answer;
            }
        }
        
        return $this->quizQuestionsAnswers;
    }

    public function getUserTest() {
        if(!$this->UserTest){
            $this->UserTest = (new QuizUserHistory([
                'user_id' => (int)Yii::$app->user->id,
                'quiz_id' => (int)$this->quiz_id
            ]))->findByCondition(['user_id','quiz_id']);
        }

        return $this->UserTest;
    }
}