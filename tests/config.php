<?php
return [
    'id' => 'tracker-tests',
    'controllerMap' => [
        'installer' => 'humhub\modules\installer\commands\InstallController',
    ],
    'components' => [
        'mailer' => [
            'useFileTransport' => true,
        ],
        'user' => [
            'class' => 'humhub\modules\user\components\User',
            'identityClass' => 'humhub\modules\user\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => ['/user/auth/login'],
        ],
        'urlManager' => [
            'showScriptName' => true,
            'baseUrl' => 'http://localhost:8080',
            'hostInfo' => 'http://localhost:8080',
        ],
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=humhub_test',
            'username' => 'test-user',
            'password' => '1',
            'charset' => 'utf8',
        ],
    ],
];
