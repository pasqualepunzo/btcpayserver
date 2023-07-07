<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Payments;

/**
 * PaymentsSearch represents the model behind the search form of `app\models\Payments`.
 */
class PaymentsSearch extends Payments
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'invoice_id', 'paymentMethodPaid'], 'integer'],
            [['paymentMethod', 'destination', 'payments', 'additionalData'], 'safe'],
            [['rate', 'totalPaid', 'due', 'amount', 'networkFee'], 'number'],
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
        $query = Payments::find();

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
            'invoice_id' => $this->invoice_id,
            'rate' => $this->rate,
            'paymentMethodPaid' => $this->paymentMethodPaid,
            'totalPaid' => $this->totalPaid,
            'due' => $this->due,
            'amount' => $this->amount,
            'networkFee' => $this->networkFee,
        ]);

        $query->andFilterWhere(['like', 'paymentMethod', $this->paymentMethod])
            ->andFilterWhere(['like', 'destination', $this->destination])
            ->andFilterWhere(['like', 'payments', $this->payments])
            ->andFilterWhere(['like', 'additionalData', $this->additionalData]);

        return $dataProvider;
    }
}
