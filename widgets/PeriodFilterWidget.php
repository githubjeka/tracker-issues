<?php

namespace tracker\widgets;

use tracker\models\IssueSearch;

/**
 * Used in gridView of issues for filters
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class PeriodFilterWidget extends \yii\bootstrap\Widget
{
    /**
     * @var IssueSearch
     */
    public $searchModel;

    public function run()
    {
        if (empty($this->searchModel)) {
            return '';
        }
        return $this->render('period_filter', ['searchModel' => $this->searchModel]);
    }
}
