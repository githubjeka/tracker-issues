<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \tracker\models\IssueSearch|null $searchModel
 * @var \humhub\modules\content\components\ContentContainerActiveRecord $contentContainer
 */
$formatter = Yii::$app->formatter;
?>
<div class="row">
    <div class="col-md-3">
        <?php if ($searchModel !== null) : ?>
            <?= $this->render('__search_index', ['searchModel' => $searchModel]) ?>
        <?php endif; ?>
    </div>
    <div class="col-md-9">
        <?= \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '__item_index',
            'emptyText' => Yii::t('TrackerIssuesModule.views', 'No open issues...'),
        ]) ?>
    </div>
</div>

