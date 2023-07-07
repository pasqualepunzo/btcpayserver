<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Invoices;

/**
 * InvoicesSearch represents the model behind the search form of `app\models\Invoices`.
 */
class InvoicesSearch extends Invoices
{
    // add the public attributes that will be used to store the data to be search
    public $merchantName;
    public $storeName;
    public $posName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'merchant_id', 'store_id', 'pos_id', 'expirationTime', 'monitoringExpiration', 'archived'], 'integer'],
            [['invoiceType', 'status', 'metadata', 'checkout', 'receipt', 'invoiceId', 'storeId', 'currency', 'type', 'checkoutLink', 'additionalStatus', 'availableStatusesForManualMarking'], 'safe'],
            [['amount'], 'number'],

            [['merchantName', 'storeName', 'posName'], 'safe'],
            // [['createdTime'], 'safe'], // messa qui per ricerca date
            ['createdTime', 'date', 'format' => 'php:d/m/Y', 'message' => 'Il formato non è valido. Usare dd/mm/yyyy'],
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
        $query = Invoices::find();

        // add conditions that should always apply here
        // Utilizza il nome del metodo del Model (getMerchant) e non il nome di tabella
        $query->joinWith(['pos', 'store', 'merchant']);

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

        // Important: here is how we set up the sorting
        // The key is the attribute name on our "ClientiSearch" instance
        $dataProvider->sort->attributes['posName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['pos.appName' => SORT_ASC],
            'desc' => ['pos.appName' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        // ricerca PER date
        if (!empty($this->createdTime)){
            $date = \DateTime::createFromFormat('d/m/Y', $this->createdTime);
        }

        $errors = \DateTime::getLastErrors();

        // echo '<pre>'.var_dump($date).'</pre>';;
        // echo '<pre>' . var_dump($date) . '</pre>';
        // exit;
        if (!$errors && isset($date)) {
            $time_start = strtotime($date->format('Y-m-d'));
            $time_end = $time_start + 86400;
            /**
             * La condizione >= nell'esempio garantisce che la ricerca includa le righe con data uguale a quella specificata. 
             * La condizione < con $timestamp + 86400 garantisce che la ricerca includa tutte le righe fino alla fine del giorno specificato 
             * (86400 secondi sono equivalenti a un giorno).
             */
            $query->andFilterWhere(['between', 'invoices.createdTime', $time_start, $time_end]);

            // $query->andFilterWhere(['>=', 'invoices.createdTime', $time_start]);
            // $query->andFilterWhere(['<', 'invoices.createdTime', $time_end]);
        }

        // echo '<pre>' . print_r($time_start, true) . '</pre>';
        // echo '<pre>' . print_r($time_end, true) . '</pre>';exit;


        // grid filtering conditions
        // utilizza il nome dalla tabella
        $query->andFilterWhere([
            'invoices.id' => $this->id,
            'invoices.merchant_id' => $this->merchant_id,
            'invoices.store_id' => $this->store_id,
            'invoices.pos_id' => $this->pos_id,
            // 'invoices.amount' => $this->amount,
            // 'invoices.createdTime' => $this->createdTime,
            'invoices.expirationTime' => $this->expirationTime,
            'invoices.monitoringExpiration' => $this->monitoringExpiration,
            'invoices.archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'invoices.invoiceType', $this->invoiceType])
            ->andFilterWhere(['like', 'invoices.status', $this->status])
            ->andFilterWhere(['like', 'invoices.amount', $this->amount]) // ricerco importo come stringa e non come numero
            ->andFilterWhere(['like', 'invoices.metadata', $this->metadata])
            ->andFilterWhere(['like', 'invoices.checkout', $this->checkout])
            ->andFilterWhere(['like', 'invoices.receipt', $this->receipt])
            ->andFilterWhere(['like', 'invoices.invoiceId', $this->invoiceId])
            ->andFilterWhere(['like', 'invoices.storeId', $this->storeId])
            ->andFilterWhere(['like', 'invoices.currency', $this->currency])
            ->andFilterWhere(['like', 'invoices.type', $this->type])
            ->andFilterWhere(['like', 'invoices.checkoutLink', $this->checkoutLink])
            ->andFilterWhere(['like', 'invoices.additionalStatus', $this->additionalStatus])
            ->andFilterWhere(['like', 'invoices.availableStatusesForManualMarking', $this->availableStatusesForManualMarking]);

        // filter search
        $query->andFilterWhere(['like', 'pos.appName', $this->posName]);
        $query->andFilterWhere(['like', 'stores.description', $this->storeName]);
        $query->andFilterWhere(['like', 'merchants.description', $this->merchantName]);    

        return $dataProvider;
    }
}
