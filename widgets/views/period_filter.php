<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 * @var $searchModel \tracker\models\IssueSearch
 * @var $this \humhub\components\View
 */
use yii\bootstrap\Html;

// this js to fix issue about case when DatePicker is showed after filter request by pjax
$this->registerJs('
    $(document).on("pjax:success", function(){
        $("#ui-datepicker-div").css( "display", "none" );        
    });
');
?>
<div>
    <?=
    Html::checkbox(
        Html::getInputName($searchModel, 'isConstantly'),
        $searchModel->isConstantly,
        ['label' => Yii::t('TrackerIssuesModule.views', 'constantly'), 'uncheck' => '0']
    ) ?>
</div>
<?php if ($searchModel->isConstantly === false) : ?>
    <div class="row">
        <div class="col-md-4">
            <?= \yii\jui\DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'startStartedDate',
                'dateFormat' => 'php:Y-m-d',
                'options' => [
                    'class' => 'form-control input-sm',
                    'placeholder' => Yii::t('TrackerIssuesModule.views', 'from'),
                ],
            ]) ?>
        </div>
        <div class="col-md-4">

            <?= \yii\jui\DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'endStartedDate',
                'dateFormat' => 'php:Y-m-d',
                'options' => [
                    'class' => 'form-control input-sm',
                    'placeholder' => Yii::t('TrackerIssuesModule.views', 'to'),
                ],
            ]) ?>

        </div>
    </div>
<?php else: ?>
    <?= \yii\jui\DatePicker::widget([
        'model' => $searchModel,
        'attribute' => 'startStartedDate',
        'dateFormat' => 'php:Y-m-d',
        'options' => [
            'class' => 'form-control input-sm',
            'placeholder' => Yii::t('TrackerIssuesModule.views', 'Date'),
        ],
    ]) ?>
<?php endif; ?>
