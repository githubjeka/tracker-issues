#!/usr/bin/env php
<?php
/**
 * Travis console bootstrap file.
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', __DIR__ . '/../../');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/tests/config-travis.php'),
    require(YII_APP_BASE_PATH . '/humhub/config/console.php'),
    (is_readable(YII_APP_BASE_PATH . '/config/dynamic.php')) ? require(YII_APP_BASE_PATH . '/config/dynamic.php') : []
);

$application = new \humhub\components\console\Application($config);
$application->params['installed'] = false;
$exitCode = $application->run();
exit($exitCode);
