<?php

namespace tracker\models;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use tracker\enum\IssueVisibilityEnum;

/**
 * Class IssueContent
 * Note: used only by Issue model
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueContent extends \humhub\modules\content\models\Content
{
    /**
     * Checks if user can view this content
     *
     * @since 1.1
     *
     * @param User $user
     *
     * @return boolean can view this content
     */
    public function canView($user = null)
    {
        if ($user === null && !\Yii::$app->user->isGuest) {
            $user = \Yii::$app->user->getIdentity();
        }

        // Check Guest Visibility
        if ($user === null) {
            if (\Yii::$app->getModule('user')->settings->get('auth.allowGuestAccess') &&
                $this->visibility === self::VISIBILITY_PUBLIC
            ) {
                // Check container visibility for guests
                if (($this->container instanceof Space && $this->container->visibility == Space::VISIBILITY_ALL) ||
                    ($this->container instanceof User && $this->container->visibility == User::VISIBILITY_ALL)
                ) {
                    return true;
                }
            }
            return false;
        }

        if ((int)$this->visibility === (int)IssueVisibilityEnum::TYPE_PUBLIC) {
            return true;
        }

        // Check Superadmin can see all content option
        if ($user->isSystemAdmin() && \Yii::$app->getModule('content')->adminCanViewAllContent) {
            return true;
        }

        if ((int)$this->visibility === (int)IssueVisibilityEnum::TYPE_PROTECTED &&
            $this->getContainer()->canAccessPrivateContent($user)
        ) {
            return true;
        }

        if ((int)$this->visibility === (int)IssueVisibilityEnum::TYPE_PRIVATE) {
            if ($this->created_by === $user->id) {
                return true;
            }

            return $this->getPolymorphicRelation()->getAssignees()->andWhere(['user_id' => $user->id])->exists();
        }


        return false;
    }
}
