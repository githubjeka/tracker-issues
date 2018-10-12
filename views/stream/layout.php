<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \tracker\models\IssueSearch $searchModel
 * @var \humhub\modules\content\components\ContentContainerActiveRecord $contentContainer
 * @var boolean $canCreateNewIssue
 */

use tracker\Module;

\tracker\assets\IssueAsset::register($this);

echo \tracker\widgets\StreamViewer::widget([
    'contentContainer' => $contentContainer,
    'streamAction' => '/' . Module::getIdentifier() . '/stream/view',
    'messageStreamEmpty' => 'messageStreamEmpty',
    'messageStreamEmptyCss' => 'messageStreamEmptyCss',
]);