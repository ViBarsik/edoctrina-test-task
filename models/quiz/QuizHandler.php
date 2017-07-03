<?php
namespace app\models\quiz;

use Yii;
use app\models\ModelHandler;

/**
 * @property Quiz $Quiz
 */
class QuizHandler extends ModelHandler
{
    private $Quiz;
    private $error = '';

    public function init() {
        $this->Quiz = new Quiz();
    }

    public function viewList(){
        $quizList = $this->Quiz->findAll();

        if(!$quizList){
            $this->error = "Quiz List is empty. Please Add New Quiz";
            return null;
        }

        return $quizList;
    }

    public function getUserTestList(){
        if(!Yii::$app->user->isGuest){
            $userTestList = $tmpMass = [];
            $userTestList = (new QuizUserHistory([
                'user_id' => (int)Yii::$app->user->id
            ]))->findAllByUser();

            if($userTestList){
                foreach ($userTestList as &$test){
                    $tmpMass[$test['quiz_id']] = $test;
                }
                $userTestList = $tmpMass;
            }
            return $userTestList;
        }
        return [];
    }

    public function create() {
        $this->Quiz->attributes = Yii::$app->request->post('Quiz');

        if (!$this->Quiz->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->Quiz->create();

            for($i = 0; $i < $this->Quiz->questions_count; $i++){
                $QuizQuestion = new QuizQuestion(['quiz_id'=>$this->Quiz->quiz_id]);
                $QuizQuestion->generateQuestion();
                $QuizQuestion->create();

                $answerCount = $QuizQuestion->generateAnswerCount();
                $answerIsTrue = $QuizQuestion->generateTrueNumberAnswer($answerCount);
                $answerIsNumber = $QuizQuestion->generateAnswersIsNumbers();
                $answers = '';

                for($j = 1; $j <= $answerCount; $j++){
                    $QuizQuestionAnswer = new QuizQuestionAnswer([
                        'quiz_id'=>$QuizQuestion->quiz_id,
                        'question_id'=>$QuizQuestion->question_id,
                        'is_true' => $answerIsTrue === $j ? true : false
                    ]);
                    $QuizQuestionAnswer->generateAnswer($answers, $answerIsNumber);
                    $QuizQuestionAnswer->create();
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e){
            $transaction->rollBack();
            throw new \Exception ($e->getMessage(), $e->getCode());
        }
    }

    public function info(){
        return $this->test();
    }

    public function test(){
        $this->Quiz->quiz_id = (int)Yii::$app->request->get('id');

        if(!$this->Quiz->findById()){
            $this->error = "Quiz not found";
            return null;
        }

        if(!$this->Quiz->getQuizQuestions()){
            $this->error = "Questions From Quiz not found";
            return null;
        }

        if(!$this->Quiz->getQuizQuestionsAnswers()){
            $this->error = "Answers From Quiz not found";
            return null;
        }

        $this->Quiz->getUserTest();

        return $this->Quiz;
    }

    public function setAnswer(){
        $answer = json_decode(Yii::$app->request->post('answer'));
        if(!$answer || !$this->answerValidate($answer)){
            return $this->getAjaxMessage('danger','Data format is incorrect');
        }

        $Answer = (new QuizQuestionAnswer([
            'quiz_id' => $answer->quiz_id,
            'question_id' => $answer->question_id,
            'answer_id'=>$answer->answer_id
        ]))->findByCondition(['answer_id','quiz_id','question_id']);

        if(!$Answer){
            return $this->getAjaxMessage('danger','Answer data is incorrect');
        }

        $QuizTest = new QuizUserHistory([
            'user_id' => (int)Yii::$app->user->id,
            'quiz_id' => (int)$answer->quiz_id
        ]);

        if(!$QuizTest->findByCondition(['user_id','quiz_id'])){
            $QuizTest->scenario = 'create';
            $QuizTest->create();
        }

        $QuizTest->answers = json_decode($QuizTest->answers, true);
        if(isset($QuizTest->answers[$answer->question_id])){
            return $this->getAjaxMessage('danger','The answer to this question has already been received!');
        }
        $QuizTest->answers[$answer->question_id] = $answer->answer_id;
        $QuizTest->answers = json_encode($QuizTest->answers);
        $QuizTest->score_true_answers += $Answer->is_true;
        $QuizTest->scenario = 'update';
        $QuizTest->update();

        if(!$Answer->is_true){
            $Answer->is_true = 1;
            $Answer->findByCondition(['quiz_id','question_id','is_true']);
            $response = $this->getAjaxMessage('warning', "Too bad, you did not guess!");
        } else {
            $response = $this->getAjaxMessage('success', "It's True! You is best of the best!");
        }

        $response['answerTrue'] = $Answer->answer_id;
        return $response;
    }

    public function saveTest(){
        $QuizTest = (new QuizUserHistory([
            'user_id' => (int)Yii::$app->user->id,
            'quiz_id' => (int)Yii::$app->request->get('id')
        ]))->findByCondition(['user_id','quiz_id']);

        if(!$QuizTest){
            $this->error = 'You not finished this quiz!';
            return false;
        }

        $this->Quiz->quiz_id = $QuizTest->quiz_id;
        $this->Quiz = $this->Quiz->findById();

        $QuizTest->answers = json_decode($QuizTest->answers, true);

        if((int)$this->Quiz->questions_count != count($QuizTest->answers)){
            $this->error = 'You answered not by all the questions !';
            return false;
        }

        $QuizTest->scenario = 'complete';
        $QuizTest->status = QuizUserHistory::STATUS_FINISH;
        $result = $QuizTest->update();

        if($result){
            if( $curl = curl_init() ) {
                curl_setopt($curl, CURLOPT_URL, Yii::$app->params['socket_url'] . '/reload?token=' . Yii::$app->session->id . '&room=' . Yii::$app->user->id . '-' . $this->Quiz->quiz_id);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl, CURLOPT_POST, true);
                $out = curl_exec($curl);
                curl_close($curl);
            } else {
                Yii::$app->session->setFlash('warning', "Lib libcurl nof found. Appliation socket incorrect working!");
            }
        }

        return $result;
    }

    public function getQuiz($scenario = 'default'){
        $this->Quiz->scenario = $scenario;
        return $this->Quiz;
    }

    public function getError() : string {
        return $this->error;
    }

    public function getAjaxMessage(string $key, string $value) : array {
        return [
            'message' =>[
                'key'=>$key,
                'value'=>$value
            ]
        ];
    }

    public function answerValidate(&$answer) {
        $answer->quiz_id = (int)$answer->quiz_id;
        $answer->question_id = (int)$answer->question_id;
        $answer->answer_id = (int)$answer->answer_id;

        if($answer->quiz_id < 1 || $answer->question_id < 1 || $answer->answer_id < 1){
            return false;
        }
        
        return true;
    }
}