<?php

return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../humhub/config/common.php'),
    [
        'id' => 'tracker-tests',
        'controllerMap' => [
            'installer' => 'humhub\modules\installer\commands\InstallController',
        ],
        'components' => [
            'mailer' => [
                'useFileTransport' => true,
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
    ]
);

