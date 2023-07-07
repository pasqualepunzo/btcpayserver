<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stores".
 *
 * @property int $id
 * @property int $merchant_id
 * @property string $description
 * @property string $create_date
 * @property string|null $close_date
 * @property int $historical
 *
 * @property Merchants $merchant
 * @property Storesettings[] $storesettings
 */
class Stores extends \yii\db\ActiveRecord
{
    // add the public attributes that will be used to store the data to be search
    public $merchantName;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'description', 'create_date'], 'required'],
            [['merchant_id', 'historical'], 'integer'],
            [['create_date', 'close_date'], 'safe'],
            [['description'], 'string', 'max' => 256],
            [['merchant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Merchants::className(), 'targetAttribute' => ['merchant_id' => 'id']],

            [['merchantName'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'merchantName' => Yii::t('app', 'Esercente'),

            'id' => Yii::t('app', 'ID'),
            'merchant_id' => Yii::t('app', 'Esercente'),
            'description' => Yii::t('app', 'Descrizione'),
            'create_date' => Yii::t('app', 'Create Date'),
            'close_date' => Yii::t('app', 'Close Date'),
            'historical' => Yii::t('app', 'Historical'),


            // attributi per settings
            'bps_storeid' => Yii::t('app', 'ID Negozio'),
            'website' => Yii::t('app', 'Website'),
            'defaultCurrency' => Yii::t('app', 'Valuta predefinita'),
            'invoiceExpiration' => Yii::t('app', 'Scadenza transazione'),
            'displayExpirationTimer' => Yii::t('app', 'Mostra Timer di scadenza'),
            'monitoringExpiration' => Yii::t('app', 'Monitoraggio della scadenza'),
            'speedPolicy' => Yii::t('app', 'Politica di velocità delle transazioni'),
            'lightningDescriptionTemplate' => Yii::t('app', 'Modello di descrizione Lightning'),
            'paymentTolerance' => Yii::t('app', 'Tolleranza sul pagamento'),
            'anyoneCanCreateInvoice' => Yii::t('app', 'Chiunque può creare una transazione'),
            'requiresRefundEmail' => Yii::t('app', 'Richiedi mail per il rimborso'),
            'checkoutType' => Yii::t('app', 'Modalità di pagamento'),
            'receipt' => Yii::t('app', 'Ricevuta'),
            'lightningAmountInSatoshi' => Yii::t('app', 'Importo Lightning in Satoshi'),
            'lightningPrivateRouteHints' => Yii::t('app', 'Indicazioni per le route private Lightning'),
            'onChainWithLnInvoiceFallback' => Yii::t('app', 'Conferma sulla blockchain con opzione di fallback su fattura LN'),
            'redirectAutomatically' => Yii::t('app', 'redirezione automaticamente'),
            'showRecommendedFee' => Yii::t('app', 'Visualizza commissione consigliata'),
            'recommendedFeeBlockTarget' => Yii::t('app', 'Numero di blocchi consigliato per la commissione'),
            'defaultLang' => Yii::t('app', 'Lingua predefinita'),
            'customLogo' => Yii::t('app', 'Logo personalizzato'),
            'customCSS' => Yii::t('app', 'Css personalizzato'),
            'htmlTitle' => Yii::t('app', 'Titolo Html'),
            'networkFeeMode' => Yii::t('app', 'Modalità di commissione di rete'),
            'payJoinEnabled' => Yii::t('app', 'Pay Join abilitato'),
            'lazyPaymentMethods' => Yii::t('app', 'Metodi di pagamento ritardati'),
            'defaultPaymentMethod' => Yii::t('app', 'Metodo di pagamento predefinito'),
            'paymentMethodCriteria' => Yii::t('app', 'Criteri dei metodi di pagamento'),
            'spread' => Yii::t('app', 'Spread'),
            'preferredSource' => Yii::t('app', 'Origine predefinita'),
            'isCustomScript' => Yii::t('app', 'Is Custom Script'),
            'effectiveScript' => Yii::t('app', 'Effective Script'),
            'derivationScheme' => Yii::t('app', 'Derivation Scheme'),
            'label' => Yii::t('app', 'Label'),
            'accountKeyPath' => Yii::t('app', 'Account Key Path'),

            'receipt_enabled' => Yii::t('app', 'Abilita pagina di ricevuta per transazioni saldate'),
            'receipt_showPayments' => Yii::t('app', 'Mostra l\'elenco dei pagamenti nella pagina di ricevuta'),
            'receipt_showQR' => Yii::t('app', 'Mostra il codice QR della ricevuta nella pagina di ricevuta'),
        ];
    }

    /**
     * Gets query for [[Merchant]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\MerchantsQuery
     */
    public function getMerchant()
    {
        return $this->hasOne(Merchants::className(), ['id' => 'merchant_id']);
    }

    /**
     * Gets query for [[Storesettings]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\StoresettingsQuery
     */
    public function getStoresettings()
    {
        return $this->hasOne(Storesettings::className(), ['store_id' => 'id']);
    }

    /**
     * Gets query for [[Webhook]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\WebhooksQuery
     */
    public function getWebhook()
    {
        return $this->hasOne(Webhooks::className(), ['store_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\StoresQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\StoresQuery(get_called_class());
    }
}
