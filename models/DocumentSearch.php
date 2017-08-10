<?php

namespace tracker\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

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
        $queryMain = Document::find()
            ->readable($user)
            ->orderBy([Document::tableName() . '.id' => SORT_DESC]);

        $queryUnion = Document::find()->byCreator($user);

        $dataProvider = new ActiveDataProvider([
            'query' => $queryMain,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $this->applyFilters($queryMain);

        $this->applyFilters($queryUnion);
        $queryMain->union($queryUnion->createCommand()->rawSql);

        return $dataProvider;
    }

    private function applyFilters(Query $query)
    {
        $documentTable = Document::tableName();

        $query->andFilterWhere([
            $documentTable . '.type' => $this->type,
            $documentTable . '.category' => $this->category,
        ]);

        $query->andFilterWhere(['like', $documentTable . '.name', $this->name])
            ->andFilterWhere(['like', $documentTable . '.number', $this->number])
            ->andFilterWhere(['like', $documentTable . '.from', $this->from])
            ->andFilterWhere(['like', $documentTable . '.to', $this->to]);
    }
}
