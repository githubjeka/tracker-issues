<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 * @var \tracker\models\Issue $object
 */

use humhub\libs\Html;
use humhub\modules\content\widgets\WallEntryControls;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\widgets\TimeAgo;
use tracker\models\Document;
use tracker\models\Issue;

$user = $object->content->createdBy;
$container = $object->content->container;
$canViewIssue = $object->getContent()->one()->canView(\Yii::$app->user->identity);
$parents = \Yii::$app->session->get('parents', []);
$childs = \Yii::$app->session->get('childs', []);
$parents[] = $object->id;
$childs[] = $object->id;
Yii::$app->session->set('parents', $parents);
Yii::$app->session->set('childs', $childs);
?>

<?php
/** @var Issue $parent */
$parent = $object->getParent()->one();
?>

<?php if ($parent === null) : ?>

    <?php if (count($object->documents) > 0) : ?>
        <?php foreach ($object->documents as $document) : ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title">
                        <strong><?= Html::encode($document->name) ?></strong>
                    </h1>
                    <div class="panel-body">

                        <p>
                            <?php if (isset(Document::categories()[$document->category])) : ?>
                                <?= Html::encode(Document::categories()[$document->category]) ?>
                            <?php endif ?>
                            <?php if ($document->number) : ?>
                                <small>
                                    <?= Html::encode($document->number) ?>
                                </small>
                            <?php endif ?>
                        </p>

                        <i title="<?= Yii::t('TrackerIssuesModule.views', 'Created At') ?>">
                            <small class="pull-right">
                                <?= Yii::$app->formatter->asDate($document->created_at, 'long') ?>
                            </small>
                        </i>
                        <?php if ($document->type) : ?>
                            <span class="label label-default">
                                <?= Html::encode(
                                    (isset(Document::types()[$document->type])) ?
                                        Document::types()[$document->type]
                                        : $document->type
                                ) ?>
                            </span>
                        <?php endif ?>
                        <?php if ($document->to && $document->from) : ?>
                            <span class="label label-default">
                                <?= Yii::t('TrackerIssuesModule.views', 'from') ?>
                                <?= Html::encode($document->from) ?>
                                <i class="fa fa-arrow-right"></i>
                            </span>
                            <span class="label label-default"><?= Html::encode($document->to) ?></span>
                        <?php elseif ($document->to) : ?>
                            <span class="label label-default">
                                <?= Yii::t('TrackerIssuesModule.views', 'To') ?>
                                <?= Html::encode($document->to) ?>
                            </span>
                        <?php elseif ($document->from) : ?>
                            <span class="label label-default">
                                <?= Yii::t('TrackerIssuesModule.views', 'from') ?>
                                <?= Html::encode($document->from) ?>
                            </span>
                        <?php endif ?>
                        <div class="clearfix"></div>
                        <hr>

                        <mark class="text-uppercase">
                            <?= \tracker\widgets\LinkToDocFileWidget::widget(['document' => $document]) ?>
                        </mark>
                        <i>
                            <small class="text-lowercase">
                                / <?= Html::a(Yii::t('TrackerIssuesModule.views', 'Specification'),
                                    [
                                        '/' . \tracker\Module::getIdentifier() . '/document/view',
                                        'id' => $document->id,
                                    ],
                                    ['class' => 'text-primary']
                                ) ?>
                            </small>
                        </i>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

<?php if ($parent !== null)  : ?>
    <?php if (!in_array($parent->id, $parents, true))  : ?>
        <?= \tracker\widgets\IssueWallEntry::widget(['contentObject' => $parent]) ?>
    <?php endif; ?>
<?php endif; ?>

<div class="panel panel-default wall_<?= $object->getUniqueId(); ?>">
    <div class="panel-body">
        <div class="media">
            <div class="stream-entry-loader"></div>

            <ul class="nav nav-pills preferences">
                <li class="dropdown">
                    <a role="button" data-toggle="collapse" class="dropdown-toggle"
                       href="#issue-details-<?= $object->getUniqueId(); ?>" aria-expanded="false"
                       aria-controls="issue-details-<?= $object->getUniqueId(); ?>">
                        <i class="fa fa-compress"></i>
                    </a>
                </li>
                <?php if ($canViewIssue) : ?>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i></a>
                        <ul class="dropdown-menu pull-right">
                            <?= WallEntryControls::widget([
                                'object' => $object,
                                'wallEntryWidget' => $wallEntryWidget,
                            ]); ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>

            <?=
            UserImage::widget([
                'user' => $user,
                'width' => 40,
                'htmlOptions' => ['class' => 'pull-left'],
            ]);
            ?>

            <?php if ($showContentContainer && $container instanceof Space): ?>
                <?=
                SpaceImage::widget([
                    'space' => $container,
                    'width' => 20,
                    'htmlOptions' => ['class' => 'img-space'],
                    'link' => 'true',
                    'linkOptions' => ['class' => 'pull-left'],
                ]);
                ?>
            <?php endif; ?>

            <div class="media-body">
                <div class="media-heading">
                    <?= Html::containerLink($user); ?>
                </div>
                <div class="media-subheading">
                    <?= TimeAgo::widget(['timestamp' => $createdAt]); ?>
                    <?php if ($updatedAt !== null) : ?>
                        &middot;
                        <span class="tt"
                              title="<?= Yii::$app->formatter->asDateTime($updatedAt); ?>"><?= Yii::t('ContentModule.base',
                                'Updated'); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <div class="content" id="wall_content_<?= $object->getUniqueId(); ?>">
                <?= $content; ?>
            </div>

            <div class="stream-entry-addons clearfix">
                <?php if ($canViewIssue) : ?>
                    <?= \humhub\modules\content\widgets\WallEntryAddons::widget(['object' => $object]); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$dataProvider = new \yii\data\ArrayDataProvider(['allModels' => $object->getSubtasks()->readable()->all()]);
?>

<?php foreach ($dataProvider->getModels() as $child) : ?>
    <?php if (!in_array($child->id, $childs, true))  : ?>
        <?= \tracker\widgets\IssueWallEntry::widget(['contentObject' => $child]) ?>
    <?php endif; ?>
<?php endforeach; ?>
