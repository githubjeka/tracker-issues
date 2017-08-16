<?php
/**
 * @var $model Document, the data model
 * @var $key mixed, the key value associated with the data item
 * @var $index  integer, the zero-based index of the data item in the items array returned by [[dataProvider]].
 * @var $widget \yii\widgets\ListView, this widget instance
 */

use humhub\libs\Html;
use tracker\models\Document;

?>

<div style="padding: 7px;border-right: 1px solid lightgrey;">

    <div class="row">
        <div class="col-md-1">
            <div class="text-right">
                <?= \tracker\widgets\LinkToDocFileWidget::widget([
                    'onlyIcon' => true,
                    'document' => $model,
                    'icon' => '<i class="fa fa-3x fa-download fa-border"></i>',
                ]) ?>
            </div>
        </div>
        <div class="col-md-11">
            <div class="row">
                <div class="col-md-4">
                    <p>
                        <?php if (isset(Document::categories()[$model->category])) : ?>
                            <?= Html::encode(Document::categories()[$model->category]) ?>
                        <?php else: ?>
                            <?= Html::encode($model->category) ?>
                        <?php endif ?>
                        <br>
                        <?php if ($model->number) : ?>
                            <small>
                                <?= Html::encode($model->number) ?>
                            </small>
                        <?php endif ?>
                        <i title="<?= Yii::t('TrackerIssuesModule.views', 'Registered at') ?>">
                            <small>
                                <?= Yii::$app->formatter->asDate($model->registered_at, 'long') ?>
                            </small>
                        </i>
                    </p>

                    <hr style="margin: 0">
                    <?php if ($model->type) : ?>
                        <span class="label label-default">
                            <?= Html::encode(
                                (isset(Document::types()[$model->type])) ?
                                    Document::types()[$model->type]
                                    : $model->type
                            ) ?>
                        </span>
                    <?php endif ?>

                    <?php if ($model->to && $model->from) : ?>
                        <span class="label label-default">
                <?= Yii::t('TrackerIssuesModule.views', 'from') ?>
                            <?= Html::encode($model->from) ?>
                            <i class="fa fa-arrow-right"></i>
            </span>
                        <span class="label label-default">
               <?= Html::encode($model->to) ?>
            </span>
                    <?php elseif ($model->to) : ?>
                        <span class="label label-default">
                <?= Yii::t('TrackerIssuesModule.views', 'To') ?> <?= Html::encode($model->to) ?>
            </span>
                    <?php elseif ($model->from) : ?>
                        <span class="label label-default">
                  <?= Yii::t('TrackerIssuesModule.views', 'from') ?> <?= Html::encode($model->from) ?>
            </span>
                    <?php endif ?>
                    <div class="clearfix"></div>

                </div>
                <div class="col-md-8">
                    <blockquote style="background-color: transparent;">
                        <?= Html::beginTag('a', [
                            'href' => \yii\helpers\Url::to([
                                '/' . \tracker\Module::getIdentifier() . '/document/view',
                                'id' => $model->id,
                            ]),
                        ]) ?>
                        <?= Html::encode($model->name) ?>
                        <?= Html::endTag('a') ?>
                    </blockquote>
                </div>
            </div>

        </div>
    </div>

</div>


