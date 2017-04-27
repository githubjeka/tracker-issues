<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 * @var \tracker\models\Issue $object
 */
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\content\widgets\WallEntryControls;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use yii\helpers\Html;

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

            <a href="<?= $user->getUrl(); ?>" class="pull-left">
                <img class="media-object img-rounded user-image user-<?= $user->guid; ?>" alt="40x40"
                     data-src="holder.js/40x40" style="width: 40px; height: 40px;"
                     src="<?= $user->getProfileImage()->getUrl(); ?>"
                     width="40" height="40"/>
            </a>

            <?php if (!Yii::$app->controller instanceof ContentContainerController &&
                      $object->content->container instanceof Space
            ): ?>
                <?= \humhub\modules\space\widgets\Image::widget([
                    'space' => $object->content->container,
                    'width' => 20,
                    'htmlOptions' => [
                        'class' => 'img-space',
                    ],
                    'link' => 'true',
                    'linkOptions' => [
                        'class' => 'pull-left',
                    ],
                ]); ?>

            <?php endif; ?>

            <div class="media-body">

                <h4 class="media-heading">
                    <a
                        href="<?= $user->getUrl(); ?>"><?= Html::encode($user->displayName); ?></a>
                    <small>

                        <?php if (!Yii::$app->controller instanceof ContentContainerController &&
                                  $container instanceof User && $container->id != $user->id
                        ): ?>
                            <i class="fa fa-caret-right" aria-hidden="true"></i>
                            <strong><a
                                    href="<?= $container->getUrl(); ?>"><?= Html::encode($container->displayName); ?></a></strong>&nbsp;
                        <?php endif; ?>

                        <?= \humhub\widgets\TimeAgo::widget(['timestamp' => $object->content->created_at]); ?>

                        <?php if ($object->content->created_at !== $object->content->updated_at &&
                                  $object->content->updated_at != ''
                        ): ?>
                            (<?= Yii::t('ContentModule.views_wallLayout', 'Updated :timeago',
                                [':timeago' => \humhub\widgets\TimeAgo::widget(['timestamp' => $object->content->updated_at])]); ?>)
                        <?php endif; ?>

                        <!-- show space name -->
                        <?php if (!Yii::$app->controller instanceof ContentContainerController &&
                                  $container instanceof Space
                        ): ?>
                            <?= Yii::t('ContentModule.views_wallLayout', 'in'); ?> <strong><a
                                    href="<?= $container->getUrl(); ?>"><?= Html::encode($container->name); ?></a></strong>&nbsp;
                        <?php endif; ?>

                    </small>
                </h4>

                <h5><?php echo Html::encode($user->profile->title); ?></h5>
            </div>
            <hr>
            <p>
                <span class="label label-info"><?= $object->getContentName(); ?></span>
                <?= \tracker\widgets\PriorityIssueWidget::widget(['priority' => $object->priority]) ?>
                <?= \tracker\widgets\VisibilityIssueWidget::widget(['visibilityContent' => $object->content->visibility]); ?>
            </p>

            <hr/>

            <div class="content" id="wall_content_<?= $object->getUniqueId(); ?>">
                <?= $content; ?>
            </div>

            <div class="stream-entry-addons clearfix">
                <?= \humhub\modules\content\widgets\WallEntryAddons::widget(['object' => $object]); ?>
            </div>
        </div>
    </div>
</div>
