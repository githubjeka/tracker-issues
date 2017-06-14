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

$user = $object->content->createdBy;
$container = $object->content->container;
?>

<div class="panel panel-default wall_<?= $object->getUniqueId(); ?>">
    <div class="panel-body">

        <div class="media">

            <div class="stream-entry-loader"></div>

            <ul class="nav nav-pills preferences">
                <li class="dropdown ">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i></a>
                    <ul class="dropdown-menu pull-right">
                        <?= WallEntryControls::widget(['object' => $object, 'wallEntryWidget' => $wallEntryWidget,]); ?>
                    </ul>
                </li>
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
                    <?php if ($showContentContainer): ?>
                        <span class="viaLink">
                            <i class="fa fa-caret-right" aria-hidden="true"></i>
                            <?= Html::containerLink($container); ?>
                        </span>
                    <?php endif; ?>

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
                <?= \humhub\modules\content\widgets\WallEntryAddons::widget(['object' => $object]); ?>
            </div>
        </div>
    </div>
</div>
