<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Merchants;

/**
 * MerchantsSearch represents the model behind the search form of `app\models\Merchants`.
 */
class MerchantsSearch extends Merchants
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'historical'], 'integer'],
            [['piva', 'description', 'address', 'email', 'create_date', 'close_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Merchants::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'create_date' => $this->create_date,
            'close_date' => $this->close_date,
            'historical' => $this->historical,
        ]);

        $query->andFilterWhere(['like', 'piva', $this->piva])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
