<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

use humhub\modules\content\widgets\richtext\RichText;
use tracker\models\Document;
use tracker\models\Link;
use tracker\Module;
use tracker\permissions\AddReceiversToDocument;
use tracker\widgets\StatusIssueWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var $this yii\web\View
 * @var $model tracker\models\Document
 */

$this->title = $model->name;
$formatter = Yii::$app->formatter;
$this->registerAssetBundle(\tracker\assets\IssueAsset::class);
/** @var \humhub\modules\user\models\User $userClass */
$userClass = Yii::$app->user->identityClass;
/** @var \humhub\modules\user\models\User|null $user */
$user = $userClass::findOne($model->created_by);
?>
<div class="document-view">

    <div class="panel">
        <div class="panel-heading">
            <?= \tracker\widgets\BackBtn::widget(['alternativeUrl' => ['index']]) ?>
            <div class="pull-right">
                <?= $this->trigger(
                    'RENDER-ACTION-BUTTONS-DOCUMENT',
                    new \yii\base\Event([
                        'sender' => $model,
                        'data' => []
                    ])
                ) ?>
                <?php if ((int)$model->created_by === (int)Yii::$app->user->id) : ?>
                    <?php $url = Url::to([
                        '/' . Module::getIdentifier() . '/document/add-file',
                        'id' => $model->id,
                    ]); ?>
                    <a href="<?= $url; ?>" class="btn btn-primary btn-sm text-uppercase" data-target="#globalModal">
                        <i class="fa fa-refresh"></i> <?= Yii::t('TrackerIssuesModule.views', 'Change file'); ?>
                    </a>
                    <?php $url = Url::to([
                        '/' . Module::getIdentifier() . '/document/change-info',
                        'id' => $model->id,
                    ]); ?>
                    <a href="<?= $url; ?>" class="btn btn-primary btn-sm text-uppercase" data-target="#globalModal">
                        <i class="fa fa-pencil"></i> <?= Yii::t('TrackerIssuesModule.views', 'Change info'); ?>
                    </a>
                <?php endif; ?>
            </div>
            <h1 class="panel-title text-center">
                <?= \tracker\widgets\DocumentCategoryLabel::widget(['category' => $model->categoryModel]) ?>
            </h1>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <fieldset>
                        <legend><?= Html::encode($this->title) ?></legend>
                        <?php if ($model->description) : ?>
                            <blockquote>
                                <?= RichText::widget([
                                    'text' => $model->description,
                                ]) ?>
                            </blockquote>
                        <?php endif; ?>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => [
                            'class' => 'table table-bordered detail-view',
                        ],
                        'attributes' => [
                            [
                                'attribute' => 'file',
                                'format' => 'raw',
                                'contentOptions' => ['class' => 'text-uppercase'],
                                'value' => '<mark>' .
                                    \tracker\widgets\LinkToDocFileWidget::widget(['document' => $model]) .
                                    '</mark>',
                            ],
                            [
                                'attribute' => 'number',
                                'format' => 'html',
                                'value' => "<b>{$model->number}</b>",
                            ],
                            'registered_at:date',
                            'from',
                            'to',
                            [
                                'attribute' => 'type',
                                'format' => 'html',
                                'value' => \tracker\widgets\DocumentTypeLabel::widget(['type' => $model->typeModel]),
                            ],
                            [
                                'label' => Yii::t('TrackerIssuesModule.views', 'Created By'),
                                'format' => 'raw',
                                'value' => $user
                                    ? '<img src="' . $user->getProfileImage()->getUrl() . '"
                                         class="img-rounded tt img_margin"
                                         height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                         style="width: 24px; height: 24px;"> ' .
                                    Html::encode($user->displayName) . ' / ' .
                                    Yii::$app->formatter->asDatetime($model->created_at) : '',
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">

            <?php if (\Yii::$app->user->can(new AddReceiversToDocument()) ||
                (int)$model->created_by === (int)Yii::$app->user->id) : ?>
                <div class="text-right">
                    <?php $url = Url::to([
                        '/' . tracker\Module::getIdentifier() . '/document/to-add-receivers',
                        'id' => $model->id,
                    ]); ?>
                    <a href="<?= $url; ?>" class="btn btn-primary btn-sm text-uppercase" data-target="#globalModal">
                        <i class="fa fa-plus"></i> <?= Yii::t('TrackerIssuesModule.views', 'Add Receivers'); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (count($model->receivers) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="text-uppercase">
                                <i class="fa fa-user" aria-hidden="true"></i>
                                <?= Yii::t('TrackerIssuesModule.views', 'Receiver') ?>
                            </th>
                            <th class="text-uppercase">
                                <i class="fa fa-calendar-o" aria-hidden="true"></i>
                                <?= Yii::t('TrackerIssuesModule.views', 'Delivered at') ?>
                            </th>
                            <th class="text-uppercase">
                                <i class="fa fa-calendar-plus-o" aria-hidden="true"></i>
                                <?= Yii::t('TrackerIssuesModule.views', 'Viewed at') ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($model->receivers as $receiver) : ?>
                            <?php if ($receiver->user) : ?>
                                <tr>
                                    <td>
                                        <img src="<?= $receiver->user->getProfileImage()->getUrl(); ?>"
                                             class="img-rounded tt img_margin"
                                             height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                             style="width: 24px; height: 24px;">
                                    </td>
                                    <td>
                                        <a href="<?= $receiver->user->getUrl(); ?>">
                                            <small><?= Html::encode($receiver->user->displayName); ?></small>
                                        </a>
                                    </td>
                                    <td>
                                        <?= $formatter->asDatetime($receiver->created_at, 'HH:mm, eee. d MMM yyyy') ?>
                                    </td>
                                    <td>
                                        <?php if ($receiver->view_mark) : ?>
                                            <?= $formatter->asDatetime($receiver->viewed_at, 'HH:mm, eee. d MMM yyyy') ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>


            <?php else: ?>
                <?= Yii::t('TrackerIssuesModule.views', 'Has no direct receivers') ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!\Yii::$app->user->isGuest && Document::find()
        ->readable(\Yii::$app->user->identity, true)
        ->byId($model->id)
        ->exists()) : ?>

    <div class="panel">
        <div class="panel-heading">
            <h1 class="panel-title text-uppercase">
        <span class="fa-stack fa-lg">
          <i class="fa fa-square-o fa-stack-2x"></i>
          <i class="fa fa-tasks fa-stack-1x"></i>
        </span> <?= Yii::t('TrackerIssuesModule.views', 'Tracker issues') ?>:
            </h1>
        </div>

        <div class="panel-body">
            <?php
            $issues = $model
                ->getIssues()
                ->leftJoin(
                    Link::tableName(),
                    \tracker\models\Issue::tableName() . '.id = child_id'
                )
                ->andWhere('parent_id IS NULL')
                ->orderBy([\tracker\models\Issue::tableName() . '.deadline' => SORT_ASC])
                ->all();
            ?>
            <?php if (count($issues) > 0) : ?>
                <?php
                function subtaskRender(\tracker\models\Issue $issue, \yii\i18n\Formatter $formatter)
                {
                    echo Html::beginTag('li', ['class' => 'size']);

                    echo Html::beginTag('i');
                    echo Html::beginTag('small');
                    echo StatusIssueWidget::widget(['status' => $issue->status]);
                    if (empty($issue->deadline) && $issue->status === \tracker\enum\IssueStatusEnum::TYPE_WORK) {
                        echo '&nbsp;';
                        echo Html::beginTag('span', ['class' => 'label label-success']);
                        echo Yii::t('TrackerIssuesModule.views', 'constantly');
                        echo Html::endTag('span');
                    }

                    if ($issue->deadline) {
                        echo '&nbsp;';
                        echo Html::beginTag('span', [
                            'data-toggle' => 'tooltip',
                            'title' => Yii::t('TrackerIssuesModule.views', 'Deadline')
                        ]);
                        echo $formatter->asDate($issue->deadline);
                        echo Html::endTag('span');
                    }

                    echo '&nbsp;';
                    $user = $issue->content->getCreatedBy()->one();
                    $createrImage = \humhub\modules\user\widgets\Image::widget([
                            'user' => $user,
                            'width' => 18,
                        ]) . '&nbsp;' . $user->displayName;
                    echo $createrImage;
                    echo '&nbsp;';
                    echo Html::endTag('small');
                    echo Html::endTag('i');

                    if ($issue->getContent()->one()->canView(\Yii::$app->user->identity)) {
                        echo Html::beginTag('b');
                        echo Html::a(
                            $issue->description ? Html::encode($issue->description)
                                : Yii::t('TrackerIssuesModule.base', 'Issue'),
                            $issue->content->getUrl(),
                            ['class' => 'text-info']
                        );
                        echo Html::endTag('b');
                    } else {
                        echo Html::encode($issue->description);
                    }
                    echo Html::beginTag('ul', ['style' => 'list-style: none;']);
                    foreach ($issue->subtasks as $subtask) {
                        subtaskRender($subtask, $formatter);
                    }
                    echo Html::endTag('ul');
                    echo Html::endTag('li');
                }

                ?>
                <ol>
                    <?php foreach ($issues as $i => $issue) : ?>
                        <?php subtaskRender($issue, $formatter) ?>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <?= Yii::t('TrackerIssuesModule.views', 'No open issues...') ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div>
