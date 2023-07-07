<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pos;

/**
 * PosSearch represents the model behind the search form of `app\models\Pos`.
 */
class PosSearch extends Pos
{
    // add the public attributes that will be used to store the data to be search
    public $merchantName;
    public $storeName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'merchant_id', 'store_id', 'historical'], 'integer'],
            [['description', 'appName', 'sin', 'create_date', 'close_date'], 'safe'],

            [['merchantName', 'storeName'], 'safe'],
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
        $query = Pos::find();

        // add conditions that should always apply here
        // Utilizza il nome del metodo del Model (getMerchant) e non il nome di tabella
        $query->joinWith(['store','merchant']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        // Important: here is how we set up the sorting
        // The key is the attribute name on our "ClientiSearch" instance
        $dataProvider->sort->attributes['merchantName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['merchant.description' => SORT_ASC],
            'desc' => ['merchant.description' => SORT_DESC],
        ];

        // Important: here is how we set up the sorting
        // The key is the attribute name on our "ClientiSearch" instance
        $dataProvider->sort->attributes['storeName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['store.description' => SORT_ASC],
            'desc' => ['store.description' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        // utilizza il nome dalla tabella
        $query->andFilterWhere([
            'pos.id' => $this->id,
            'pos.merchant_id' => $this->merchant_id,
            'pos.store_id' => $this->store_id,
            'pos.create_date' => $this->create_date,
            'pos.close_date' => $this->close_date,
            'pos.historical' => $this->historical,
        ]);

        $query->andFilterWhere(['like', 'pos.description', $this->description])
            ->andFilterWhere(['like', 'pos.appName', $this->appName])
            ->andFilterWhere(['like', 'pos.sin', $this->sin]);

        // filter search
        $query->andFilterWhere(['like', 'stores.description', $this->storeName]);
        $query->andFilterWhere(['like', 'merchants.description', $this->merchantName]);

        return $dataProvider;
    }
}
