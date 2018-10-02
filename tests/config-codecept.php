<?php

return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../humhub/config/common.php'),
    require(__DIR__ . '/config.php')
);

