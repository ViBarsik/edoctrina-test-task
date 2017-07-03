<?php
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'L2l32lcIxjjej20d30',
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'user' => [
            'identityClass' => 'app\models\user\UserIdentity',
            'enableAutoLogin' => true,
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'site/index',
                '/quiz' => 'quiz/index',
                '/install' => 'install/index',

                '/<action:[a-zA-Z0-9\-]{2,50}>' => 'site/<action>',
                '/<action:[a-zA-Z0-9\-]{2,50}>/<id:[0-9]{1,11}>' => 'site/<action>',
                '<controller:[a-zA-Z0-9\-]{2,50}>/<action:[a-zA-Z0-9\-]{2,50}>' => '<controller>/<action>',
                '<controller:[a-zA-Z0-9\-]{2,50}>/<action:[a-zA-Z0-9\-]{2,50}>/<id:[0-9]{1,11}>' => '<controller>/<action>',
            ],
        ],

        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . $params['mysql_host'] . ';dbname=' . $params['mysql_database'],
            'username' => $params['mysql_user'],
            'password' => $params['mysql_password'],
            'charset' => 'utf8',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {

}

return $config;
