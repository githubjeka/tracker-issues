<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this View
 * @var ActiveDataProvider $dataProvider
 * @var IssueSearch|null $searchModel
 * @var ContentContainerActiveRecord $contentContainer
 */
$formatter = Yii::$app->formatter;

use humhub\components\View;
use humhub\modules\content\components\ContentContainerActiveRecord;
use tracker\models\IssueSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

?>
<div class="row">
  <div class="col-md-3">
      <?php if ($searchModel !== null) : ?>
          <?= $this->render('__search_index', ['searchModel' => $searchModel]) ?>
      <?php endif; ?>
  </div>
  <div class="col-md-9">
    <div class="text-center">
        <?= Html::a(
            Yii::t('TrackerIssuesModule.views', 'old first'),
            Url::current(['sort' => 'deadline']),
            ['class' => 'btn btn-link']
        ) ?>
        <?= Html::a(Yii::t('TrackerIssuesModule.views', 'new first'),
            Url::current(['sort' => '-deadline']),
            ['class' => 'btn btn-link']
        ) ?>
    </div>
      <?= ListView::widget([
          'dataProvider' => $dataProvider,
          'itemView' => '__item_index',
          'emptyText' => Yii::t('TrackerIssuesModule.views', 'No open issues...'),
      ]) ?>
  </div>
</div>

