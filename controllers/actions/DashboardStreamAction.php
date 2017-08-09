<?php


namespace tracker\controllers\actions;

use humhub\modules\dashboard\components\actions\DashboardStream;
use tracker\models\Issue;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DashboardStreamAction extends DashboardStream
{
    public function setupCriteria()
    {
        if (empty($this->streamQuery->contentId)) {
            $this->streamQuery
                ->query()
                ->andWhere(
                    'content.object_model <> :className',
                    [':className' => Issue::class]
                );
        }
    }
}
