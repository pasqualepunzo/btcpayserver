<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoices".
 *
 * @property int $id
 * @property int $merchant_id
 * @property int $store_id
 * @property int $pos_id
 * @property string $invoiceType
 * @property string $status
 * @property string|null $metadata
 * @property string|null $checkout
 * @property string|null $receipt
 * @property string|null $invoiceId
 * @property string|null $storeId
 * @property float|null $amount
 * @property string|null $currency
 * @property string|null $type
 * @property string|null $checkoutLink
 * @property int|null $createdTime
 * @property int|null $expirationTime
 * @property int|null $monitoringExpiration
 * @property string|null $additionalStatus
 * @property string|null $availableStatusesForManualMarking
 * @property int|null $archived
 *
 * @property Merchants $merchant
 * @property Payments[] $payments
 * @property Pos $pos
 * @property Stores $store
 */
class Invoices extends \yii\db\ActiveRecord
{
    // add the public attributes that will be used to store the data to be search
    public $merchantName;
    public $storeName;
    public $posName;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'store_id', 'pos_id', 'invoiceType', 'status'], 'required'],
            [['merchant_id', 'store_id', 'pos_id', 'createdTime', 'expirationTime', 'monitoringExpiration', 'archived'], 'integer'],
            [['metadata', 'checkout', 'receipt', 'availableStatusesForManualMarking'], 'safe'],
            [['amount'], 'number'],
            [['invoiceType', 'status', 'currency', 'type', 'additionalStatus'], 'string', 'max' => 20],
            [['invoiceId'], 'string', 'max' => 60],
            [['storeId', 'checkoutLink'], 'string', 'max' => 512],
           
            [['merchant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Merchants::class, 'targetAttribute' => ['merchant_id' => 'id']],
            [['pos_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pos::class, 'targetAttribute' => ['pos_id' => 'id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::class, 'targetAttribute' => ['store_id' => 'id']],

            [['merchantName', 'storeName', 'posName'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'merchantName' => Yii::t('app', 'Esercente'),
            'storeName' => Yii::t('app', 'Negozio'),
            'posName' => Yii::t('app', 'Pos'),
            
            'id' => Yii::t('app', 'ID'),
            'merchant_id' => Yii::t('app', 'Esercente'),
            'store_id' => Yii::t('app', 'Negozio'),
            'pos_id' => Yii::t('app', 'Pos'),
            'invoiceType' => Yii::t('app', 'Network'),
            'status' => Yii::t('app', 'Stato'),
            'metadata' => Yii::t('app', 'Metadata'),
            'checkout' => Yii::t('app', 'Pagamento'),
            'receipt' => Yii::t('app', 'Ricevuta'),
            'invoiceId' => Yii::t('app', 'ID Transazione'),
            'storeId' => Yii::t('app', 'ID Negozio'),
            'amount' => Yii::t('app', 'Importo'),
            'currency' => Yii::t('app', 'Valuta'),
            'type' => Yii::t('app', 'Tipo'),
            'checkoutLink' => Yii::t('app', 'Link del pagamento'),
            'createdTime' => Yii::t('app', 'Data'),
            'expirationTime' => Yii::t('app', 'Data Scadenza'),
            'monitoringExpiration' => Yii::t('app', 'Monitoraggio Scadenza'),
            'additionalStatus' => Yii::t('app', 'Stati addizionali'),
            'availableStatusesForManualMarking' => Yii::t('app', 'Stati disponibili per modifica manuale'),
            'archived' => Yii::t('app', 'Archiviata'),
        ];
    }

    /**
     * Gets query for [[Merchant]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\MerchantsQuery
     */
    public function getMerchant()
    {
        return $this->hasOne(Merchants::class, ['id' => 'merchant_id']);
    }

    /**
     * Gets query for [[Payments]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\PaymentsQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payments::class, ['invoice_id' => 'id']);
    }

    /**
     * Gets query for [[Pos]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\PosQuery
     */
    public function getPos()
    {
        return $this->hasOne(Pos::class, ['id' => 'pos_id']);
    }

    /**
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\StoresQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::class, ['id' => 'store_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\InvoicesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\InvoicesQuery(get_called_class());
    }
}
