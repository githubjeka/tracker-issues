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

        if (!empty($this->tag)) {
            $query->joinWith(['personalTags'])->andWhere([Tag::tableName() . '.id' => $this->tag]);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['deadline' => SORT_ASC],
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

        $query->andFilterWhere(['IN', Issue::tableName() . '.status', $this->status]);

        if ($this->startStartedDate && $this->endStartedDate) {
            $query->andWhere([
                'BETWEEN',
                Issue::tableName() . '.started_at',
                new Expression('cast(:start as date)', [':start' => $this->startStartedDate]),
                new Expression('cast(:start as date)', [':start' => $this->endStartedDate]),
            ]);
        } elseif ($this->endStartedDate) {
            $query->andWhere(['<=', Issue::tableName() . '.started_at', $this->endStartedDate]);
        } elseif ($this->startStartedDate) {
            $query->andWhere(['>=', Issue::tableName() . '.started_at', $this->startStartedDate]);
        }
        return $dataProvider;
    }
}
