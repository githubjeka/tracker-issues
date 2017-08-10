<?php

namespace tracker\models;

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

    /**
     * @param IdentityInterface $user
     *
     * @return $this
     */
    public function byCreator(IdentityInterface $user)
    {
        return $this->andWhere([Document::tableName() . '.created_by' => $user->getId()]);
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
