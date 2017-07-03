<?php
namespace app\models\traits;

use app\models\quiz\Quiz;

/**
 * @property Quiz $Quiz
 */
trait TQuiz
{
    private $Quiz;
    public function getQuiz(){
        if(!$this->Quiz) {
            $this->Quiz = (new Quiz(['quiz_id' => $this->quiz_id]))->findById();
        }

        return $this->Quiz;
    }
}