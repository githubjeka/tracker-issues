<?php

namespace tracker\models;

use humhub\modules\content\models\Content;
use tracker\permissions\ViewAllDocuments;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DocumentQuery extends ActiveQuery
{
    /**
     * Only returns user readable records via issues readable
     *
     * @param IdentityInterface|null $user
     *
     * @return static
     */
    public function readable(IdentityInterface $user)
    {
        if ($user === null && !\Yii::$app->user->isGuest) {
            $user = \Yii::$app->user->getIdentity();
        }

        if ($user === null) { // Not available for guests.
            return $this->andWhere('1=0');
        }

        if (\Yii::$app->user->permissionmanager->can(new ViewAllDocuments())) {
            return $this;
        }

        $this
            ->joinWith([
                'issues' => function (IssueQuery $query) use ($user) {
                    return $query->readable($user);
                },
            ])
            ->groupBy(Document::tableName() . '.id');

        return $this;
    }

    public function byCreator(IdentityInterface $user)
    {
        $contentTable = Content::tableName();
        $documentTable = Document::tableName();
        $documentClass = Document::class;
        $this
            ->leftJoin(['cd' => $contentTable],
                "cd.object_id = $documentTable.id")
            ->orWhere("cd.object_model = :documentClass AND cd.created_by = :userId",
                [':userId' => $user->getId(), ':documentClass' => $documentClass])
            ->groupBy(Document::tableName() . '.id');

        return $this;
    }

    /**
     * @inheritdoc
     * @return $this
     */
    public function byId($id)
    {
        return $this->andWhere([Document::tableName() . '.id' => $id]);
    }

    /**
     * @inheritdoc
     * @return Document[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Document|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
