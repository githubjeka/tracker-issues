<?php

namespace tracker\controllers\services;

use tracker\controllers\requests\IssueRequest;
use tracker\models\Issue;
use yii\base\Object;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueService extends Object
{
    /** @var IssueRequest */
    protected $requestForm;

    public function __construct($config = [])
    {
        $this->requestForm = new IssueRequest();
        parent::__construct($config);
    }

    /** @var Issue */
    protected $issueModel;

    public function getIssueForm()
    {
        return $this->requestForm;
    }

    /**
     * @param $datum
     * @param null $formName
     * @see Model::load()
     * @return bool
     */
    public function load($datum, $formName = null)
    {
        return $this->requestForm->load($datum, $formName);
    }
}
