<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payments".
 *
 * @property int $id
 * @property int $invoice_id
 * @property string|null $paymentMethod
 * @property string|null $destination
 * @property float|null $rate
 * @property int|null $paymentMethodPaid
 * @property float|null $totalPaid
 * @property float|null $due
 * @property float|null $amount
 * @property float|null $networkFee
 * @property string|null $payments
 * @property string|null $additionalData
 *
 * @property Invoices $invoice
 */
class Payments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoice_id'], 'required'],
            [['invoice_id', 'paymentMethodPaid'], 'integer'],
            [['rate', 'totalPaid', 'due', 'amount', 'networkFee'], 'number'],
            [['payments', 'additionalData'], 'safe'],
            [['paymentMethod'], 'string', 'max' => 512],
            [['destination'], 'string', 'max' => 2048],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoices::class, 'targetAttribute' => ['invoice_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'invoice_id' => Yii::t('app', 'Invoice ID'),
            'paymentMethod' => Yii::t('app', 'Payment Method'),
            'destination' => Yii::t('app', 'Destination'),
            'rate' => Yii::t('app', 'Rate'),
            'paymentMethodPaid' => Yii::t('app', 'Payment Method Paid'),
            'totalPaid' => Yii::t('app', 'Total Paid'),
            'due' => Yii::t('app', 'Due'),
            'amount' => Yii::t('app', 'Amount'),
            'networkFee' => Yii::t('app', 'Network Fee'),
            'payments' => Yii::t('app', 'Payments'),
            'additionalData' => Yii::t('app', 'Additional Data'),
        ];
    }

    /**
     * Gets query for [[Invoice]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\InvoicesQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoices::class, ['id' => 'invoice_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\PaymentsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\PaymentsQuery(get_called_class());
    }
}
