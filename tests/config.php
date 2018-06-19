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
            'user' => [
                'class' => 'humhub\modules\user\components\User',
                'identityClass' => 'humhub\modules\user\models\User',
                'enableAutoLogin' => false,
                'enableSession' => false,
                'loginUrl' => ['/user/auth/login'],
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
    ],
    (is_readable(__DIR__ . '/config-local.php')) ?  require(__DIR__ . '/config-local.php') : []

);

