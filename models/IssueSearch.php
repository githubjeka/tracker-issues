<?php

namespace tracker\models;

use tracker\enum\IssueStatusEnum;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * IssueSearch represents the model behind the search form about `tracker\models\Issue`.
 */
class IssueSearch extends Model
{
    public $status;
    public $tag;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
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
            return $dataProvider;
        }

        $query->andFilterWhere(['IN', Issue::tableName() . '.status', $this->status]);
        return $dataProvider;
    }
}
