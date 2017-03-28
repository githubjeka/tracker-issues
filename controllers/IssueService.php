<?php

namespace tracker\controllers;

use tracker\models\Issue;
use yii\base\Object;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueService extends Object
{
    /** @var IssueRequest */
    protected $requestForm;

    /** @var Issue */
    protected $issueModel;

    public function getIssueForm()
    {
        return $this->requestForm;
    }

    public function load($datum)
    {
        return $this->requestForm->load($datum);
    }
}
