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

use yii\helpers\Url;

\tracker\assets\IssueAsset::register($this);
?>

<div class="panel panel-default">
  <div class="panel-body">

      <?php if (!($contentContainer instanceof \humhub\modules\space\models\Space)) : ?>
        <div class="pull-right">
            <?= \yii\helpers\Html::a(
                Yii::t('TrackerIssuesModule.views', 'Grid'),
                ['/' . tracker\Module::getIdentifier() . '/dashboard/issues', 'list' => 0],
                ['class' => 'btn btn-link']
            ); ?>
            <?= \yii\helpers\Html::a(
                Yii::t('TrackerIssuesModule.views', 'Timeline'),
                ['/' . tracker\Module::getIdentifier() . '/dashboard/timeline'],
                ['class' => 'btn btn-link']
            ); ?>
            <?= \yii\helpers\Html::a(
                Yii::t('TrackerIssuesModule.views', 'Calendar'),
                ['/' . tracker\Module::getIdentifier() . '/calendar/index'],
                ['class' => 'btn btn-link']
            ); ?>
        </div>
      <?php endif; ?>

      <?php if ($canCreateNewIssue): ?>
          <?php if ($contentContainer instanceof \humhub\modules\space\models\Space) {
              $url = $contentContainer->createUrl('issue/create');
          } else {
              $url = Url::to(['/' . tracker\Module::getIdentifier() . '/dashboard/to-create-issue']);
          }
          ?>
        <a href="<?= $url; ?>" class="btn btn-primary text-uppercase" data-target="#globalModal">
          <i class="fa fa-plus"></i> <?= Yii::t('TrackerIssuesModule.views', 'New issue'); ?>
        </a>
        <div class="clearfix"></div>
        <hr>
      <?php endif; ?>

      <?= $this->render('__index', [
          'contentContainer' => $contentContainer,
          'dataProvider' => $dataProvider,
          'searchModel' => $searchModel,
      ]) ?>

  </div>
</div>




