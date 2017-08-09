<?php

namespace tracker\models;

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
    public $status;

    public $startStartedDate;
    public $endStartedDate;
    public $tag;

    /**
     * The filter to return issues by which permanent work is being conducted.
     * If true will be returned issues without deadline. $endStartedDate will be set to NULL
     * If false will be returned issues with deadline.
     * If null then filter by deadline is not applied.
     *
     * @var null|boolean
     */
    public $isConstantly;

    /**
     * Returns dataProvider when model is valid only.
     *
     * @var bool
     */
    public $nullIfError = false;

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
                ['endStartedDate',],
                'filter',
                'filter' => function () {
                    return null;
                },
                'when' => function (IssueSearch $model) {
                    return $model->isConstantly === true;
                },
            ],
            ['isConstantly', 'boolean'],
            ['tag', 'in', 'range' => $this->listTags(true), 'allowArray' => true],
            ['startStartedDate', 'date', 'format' => 'php:Y-m-d'],
            ['endStartedDate', 'date', 'format' => 'php:Y-m-d'],
            ['status', 'in', 'range' => array_keys(IssueStatusEnum::getList()), 'allowArray' => true],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param null $contentContainer
     *
     * @return ActiveDataProvider
     */
    public function search($params, $contentContainer = null)
    {
        $query = Issue::find()->readable();

        if ($contentContainer !== null) {
            $query->contentContainer($contentContainer);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['deadline' => SORT_ASC],
            ],
            'pagination'=>[
                'defaultPageSize'=>50,
            ]
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

        if ($this->isConstantly === true) {
            $query->withoutDeadline();
        } elseif ($this->isConstantly === false) {
            $query->withDeadline();
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
        return $dataProvider;
    }

    public function listTags($onlyKey = false)
    {
        $list = \yii\helpers\ArrayHelper::map(
            \tracker\models\Tag::find()->byUser(\Yii::$app->user->id)->all(),
            'id',
            'name'
        );

        if ($list === []) {
            return [];
        }

        if ($onlyKey) {
            return array_keys($list);
        }

        return $list;
    }
}
