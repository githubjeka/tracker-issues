<?php

namespace tracker\models;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\user\models\User;
use tracker\enum\IssueStatusEnum;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * IssueSearch represents the model behind the search form about `tracker\models\Issue`.
 */
class IssueSearch extends Model
{
    public $title;
    public $status;

    public $startStartedDate;
    public $endStartedDate;
    public $tag;
    public $document;

    /**
     * Filter by Space
     *
     * @var string|null
     */
    public $space;

    /**
     * The filter to return issues by which permanent work is being conducted.
     * If true will be returned issues which marked by $continuous_use
     * If false will be returned issues which non marked by $continuous_use
     *
     * @var boolean
     */
    public $isConstantly = false;

    /**
     * The filter to return issues by which permanent work is being conducted.
     * If true will be returned issues without deadline. $endStartedDate will be set to NULL
     * If false will be returned issues with deadline.
     *
     * @var boolean
     */
    public $onlyWithoutDeadline = false;

    /**
     * Returns dataProvider when model is valid only.
     *
     * @var bool
     */
    public $nullIfError = false;

    /**
     * '1' => Filter by Assigner for current user and all issues without assigners
     * '2' => Filter by Assigner for current user only
     * '0' => no filter by it
     *
     * @var integer
     */
    public $onlyNotFulfilled;

    /**
     * Guids of assignees
     *
     * @var string[]
     */
    public $assignee = [];

    /**
     * True - all assignees marked issue as finished. False - not all of assignees.
     *
     * @var null|bool
     */
    public $canBeFinished = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                'isConstantly',
                'filter',
                'filter' => function ($value) {
                    return $value == true ? true : false;
                },
                'skipOnEmpty' => true,
                'skipOnArray' => true,
            ],
            [
                'onlyWithoutDeadline',
                'filter',
                'filter' => function ($value) {
                    return $value == true ? true : false;
                },
                'skipOnEmpty' => true,
                'skipOnArray' => true,
            ],
            [
                ['endStartedDate',],
                'filter',
                'filter' => function () {
                    return null;
                },
                'when' => function (IssueSearch $model) {
                    return $model->isConstantly === true;
                },
            ],
            [
                'onlyNotFulfilled',
                'default',
                'value' => 0,
                'when' => function ($model) {
                    return !$model->isConstantly;
                },
            ],
            [['title', 'document'], 'string', 'max' => 255],
            [['isConstantly', 'onlyWithoutDeadline', 'canBeFinished'], 'boolean'],
            [['onlyNotFulfilled',], 'in', 'range' => ['0', '1', '2']],
            ['tag', 'in', 'range' => $this->listTags(true), 'allowArray' => true],
            ['startStartedDate', 'date', 'format' => 'php:Y-m-d'],
            ['endStartedDate', 'date', 'format' => 'php:Y-m-d'],
            ['space', 'safe'],
            ['status', 'in', 'range' => array_keys(IssueStatusEnum::getList()), 'allowArray' => true],
            ['assignee', 'each', 'rule' => ['string']],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param null|ContentContainerActiveRecord $contentContainer
     * @return ActiveDataProvider
     */
    public function search($params, $contentContainer = null)
    {
        $tableIssue = Issue::tableName();
        $tableLink = Link::tableName();
        $tableContent = Content::tableName();
        $tableContentContainer = ContentContainer::tableName();

        $query = Issue::find()->readable()
            ->leftJoin(Link::tableName(), "$tableLink.child_id = $tableIssue.id")
            ->andWhere(
                "$tableLink.child_id IS NULL OR ($tableLink.child_id = $tableIssue.id AND $tableContent.created_by != :user)",
                [':user' => \Yii::$app->user->id]
            );

        if ($contentContainer !== null) {
            $query->contentContainer($contentContainer);
        }

        $query->joinWith(['documents']);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'document' => [
                        'asc' => [Document::tableName() . '.number' => SORT_ASC,],
                        'desc' => [Document::tableName() . '.number' => SORT_DESC],
                    ],
                    'deadline',
                    'title',
                    'priority',
                    'status',
                ],
                'defaultOrder' => ['deadline' => SORT_ASC],
            ],
            'pagination' => [
                'defaultPageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            if ($this->nullIfError) {
                $query->where('1=0');
                return $dataProvider;
            } else {
                return $dataProvider;
            }
        }

        if (!empty($this->tag)) {
            $query->joinWith(['personalTags']);
            $query->andWhere(['IN', Tag::tableName() . '.id', $this->tag]);
        }

        if ($this->space) {
            $query->andWhere(['IN', $tableContentContainer . '.guid', $this->space]);
        }

        if ($this->isConstantly === true) {
            $query->constantly();
            $query->orderBy(['priority' => SORT_DESC]);
        } else {
            $query->nonConstantly();
            if ($this->onlyWithoutDeadline === true) {
                $query->withoutDeadline();
                $query->orderBy(['priority' => SORT_DESC]);
            } else {
                $query->withDeadline();
            }
        }

        $query->andFilterWhere(['IN', Issue::tableName() . '.status', $this->status]);

        if ($this->startStartedDate && $this->endStartedDate) {
            $query->andWhere([
                'BETWEEN',
                Issue::tableName() . '.started_at',
                new Expression('cast(:start as date)', [':start' => $this->startStartedDate]),
                new Expression('cast(:end as date)', [':end' => $this->endStartedDate]),
            ]);
        } elseif ($this->endStartedDate) {
            $query->andWhere(['<', Issue::tableName() . '.started_at', $this->endStartedDate]);
        } elseif ($this->startStartedDate) {
            $query->andWhere(['>=', Issue::tableName() . '.started_at', $this->startStartedDate]);
        }

        if ($this->onlyNotFulfilled == 1) {
            $query->andWhere([
                'OR',
                [Assignee::tableName() . '.finish_mark' => false,],
                new Expression(Assignee::tableName() . '.finish_mark IS NULL'),
            ]);
        } elseif ($this->onlyNotFulfilled == 2) {
            $query->andWhere([
                Assignee::tableName() . '.finish_mark' => false,
            ]);
        }

        $query->andFilterWhere(['LIKE', Issue::tableName() . '.title', $this->title]);
        $query->andFilterWhere([
                'OR',
                ['LIKE', Document::tableName() . '.name', $this->document],
                ['LIKE', Document::tableName() . '.number', $this->document],
            ]
        );

        if (!empty($this->assignee)) {
            $query
                ->leftJoin(['all_assignee' => Assignee::tableName()], "all_assignee.issue_id = $tableIssue.id")
                ->leftJoin(['user_a' => User::tableName()], 'user_a.id = all_assignee.user_id')
                ->andWhere(['IN', 'user_a.guid', $this->assignee]);
        }

        $query->andFilterWhere(['can_be_finished' => $this->canBeFinished]);

        return $dataProvider;
    }

    public function listTags($onlyKey = false)
    {
        $list = \yii\helpers\ArrayHelper::map(
            \tracker\models\Tag::find()
                ->byUser(\Yii::$app->user->id)
                ->orderBy([\tracker\models\Tag::tableName() . '.name' => SORT_ASC])
                ->all(),
            'id', 'name'
        );;

        if ($list === []) {
            return [];
        }

        if ($onlyKey) {
            return array_keys($list);
        }

        return $list;
    }
}
