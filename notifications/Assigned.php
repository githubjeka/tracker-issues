<?php

namespace humhub\modules\tasks\notifications;

use humhub\modules\notification\components\BaseNotification;

class Assigned extends BaseNotification
{

    public $moduleId = 'tasks';

    public $viewName = "assigned";

}

?>
