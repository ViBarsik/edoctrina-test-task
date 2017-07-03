<?php
namespace app\models\install;

use Yii;

class Generator
{
    public static function createTables() {
        return Yii::$app->db->createCommand("
            CREATE TABLE IF NOT EXISTS `user` (
                `user_id` INT(11) NOT NULL AUTO_INCREMENT,
                `login` VARCHAR(100) NOT NULL,
                `email` VARCHAR(100) NOT NULL,
                `password` VARCHAR(100) NOT NULL,
                `create_time` INT(11) NOT NULL,
                `auth_key` VARCHAR(50) NOT NULL,
                `access_token` VARCHAR(50) NOT NULL,
                PRIMARY KEY (`user_id`),
                UNIQUE INDEX `email` (`email`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB;

            CREATE TABLE IF NOT EXISTS `quiz` (
                `quiz_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) NOT NULL,
                `quiz` VARCHAR(255) NOT NULL,
                `questions_count` TINYINT(4) NOT NULL,
                `create_time` INT(11) NOT NULL,
                `update_time` INT(11) NOT NULL,
                PRIMARY KEY (`quiz_id`),
                INDEX `user_id` (`user_id`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB;
            
            CREATE TABLE IF NOT EXISTS `quiz_question` (
                `question_id` INT(11) NOT NULL AUTO_INCREMENT,
                `quiz_id` MEDIUMINT(9) NOT NULL,
                `question` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`question_id`),
                INDEX `quiz_id` (`quiz_id`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB;
            
            CREATE TABLE IF NOT EXISTS `quiz_question_answer` (
                `answer_id` INT(11) NOT NULL AUTO_INCREMENT,
                `question_id` INT(11) NOT NULL,
                `quiz_id` MEDIUMINT(9) NOT NULL,
                `answer` VARCHAR(50) NOT NULL,
                `is_true` TINYINT(1) NOT NULL,
                PRIMARY KEY (`answer_id`),
                INDEX `where_quiz` (`quiz_id`, `question_id`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB;
            
            CREATE TABLE IF NOT EXISTS `quiz_user_history` (
                `history_id` INT(11) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) NOT NULL,
                `quiz_id` MEDIUMINT(9) NOT NULL,
                `answers` JSON NOT NULL,
                `status` TINYINT(1) NOT NULL DEFAULT '0',
                `score_true_answers` TINYINT(4) NOT NULL DEFAULT '0',
                `score_percent` TINYINT(4) NOT NULL DEFAULT '0',
                `create_time` INT(11) NOT NULL,
                `update_time` INT(11) NOT NULL,
                PRIMARY KEY (`history_id`),
                INDEX `user_id` (`user_id`),
                INDEX `quiz_id` (`quiz_id`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB ROW_FORMAT=DYNAMIC;
        ")->execute();
    }

    public static function updateConfig () {
        $params = Yii::$app->params;
        $params['isInstall'] = true;

        $fileObj = fopen(Yii::$aliases['@app'].'/config/params.php', "w");
        fwrite($fileObj, '<?php ' . "\r\n");
        fwrite($fileObj, 'return ' . var_export($params,true) . ';');
        fclose($fileObj );

        return true;
    }
}