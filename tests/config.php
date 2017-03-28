<?php

return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../humhub/config/common.php'),
    [
        'id' => 'tracker-tests',
        'components' => [
            'mailer' => [
                'useFileTransport' => true,
            ],
            'urlManager' => [
                'showScriptName' => true,
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

