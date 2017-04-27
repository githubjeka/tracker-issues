<?php

namespace tracker\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * TagSearch represents the model behind the search form about `tracker\models\Tag`.
 */
class TagSearch extends Model
{
    public $tag;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag'], 'string'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param null $userId personal or all
     *
     * @return ActiveDataProvider
     */
    public function search($params, $userId = null)
    {
        $query = Tag::find();

        if ($userId !== null) {
            $query->byUser($userId);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'OR',
            ['like', 'name', $this->tag],
            ['like', 'description', $this->tag],
        ]);

        return $dataProvider;
    }
}
