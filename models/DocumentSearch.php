<?php

namespace tracker\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * DocumentSearch represents the model behind the search form about `tracker\models\Document`.
 */
class DocumentSearch extends Model
{
    public $type;
    public $category;
    public $name;
    public $number;
    public $to;
    public $from;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type',], 'string'],
            [['name', 'number', 'category', 'from', 'to'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param \yii\web\IdentityInterface $user
     *
     * @return ActiveDataProvider
     */
    public function search($params, \yii\web\IdentityInterface $user)
    {
        /**
         * Search documents by issues and permissions
         */
        $queryMain = Document::find()->readable($user);
        $queryUnion = Document::find()->byCreator($user);
        $alias = 'u_q';

        $query = $this->unionToAlias($alias, $queryMain, $queryUnion);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['registered_at' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query
            ->andFilterWhere([$alias . '.type' => $this->type, $alias . '.category' => $this->category,])
            ->andFilterWhere(['like', $alias . '.name', $this->name])
            ->andFilterWhere(['like', $alias . '.number', $this->number])
            ->andFilterWhere(['like', $alias . '.from', $this->from])
            ->andFilterWhere(['like', $alias . '.to', $this->to]);

        return $dataProvider;
    }

    private function unionToAlias($alias, ActiveQuery $query, ActiveQuery $queryUnion)
    {
        $query = clone $query;
        $query->union($queryUnion);
        $newQuery = new ActiveQuery($query->modelClass);
        $newQuery->from([$alias => $query]);

        return $newQuery;
    }
}
