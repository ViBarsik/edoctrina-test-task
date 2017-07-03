<?php
namespace app\models\traits;

use app\models\quiz\QuizQuestion;

/**
 * @property QuizQuestion $QuizQuestion
 */
trait TQuizQuestion
{
    private $QuizQuestion;
    public function getQuizQuestion(){
        if(!$this->QuizQuestion) {
            $this->QuizQuestion = (new QuizQuestion(['question_id' => $this->question_id]))->findById();
        }

        return $this->QuizQuestion;
    }
}