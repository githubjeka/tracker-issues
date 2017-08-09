<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var \tracker\controllers\requests\IssueRequest $issueForm
 */
?>

<div>
    <?php
    /**
     * Note: Bug
     * If submitAjax is true then will be strange bug with cache.
     * To reproduce: set submitAjax in true and in Controller write return $this->renderAjaxContent($issue->getWallOut());
     * Create issues with one assigner. After click edit and delete one assigner,
     * content reload and you will see old model with the assigner. After F5 will see new model.
     */
    ?>
    <?= $this->render('form', ['issueForm' => $issueForm, 'submitAjax' => false]) ?>
</div>
