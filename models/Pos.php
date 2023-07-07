<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pos".
 *
 * @property int $id
 * @property int $merchant_id
 * @property int $store_id
 * @property string $appName
 * @property string $description
 * @property string|null $sin
 * @property string $create_date
 * @property string|null $close_date
 * @property int $historical
 *
 * @property Merchants $merchant
 * @property Stores $store
 * @property Possettings[] $possettings
 */
class Pos extends \yii\db\ActiveRecord
{
    // add the public attributes that will be used to store the data to be search
    public $merchantName;
    public $storeName;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'store_id', 'description', 'appName', 'create_date'], 'required'],
            [['merchant_id', 'store_id', 'historical'], 'integer'],
            [['create_date', 'close_date'], 'safe'],
            [['description'], 'string', 'max' => 512],
            [['appName'], 'string', 'max' => 30],
            [['sin'], 'string', 'max' => 255],
            [['merchant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Merchants::className(), 'targetAttribute' => ['merchant_id' => 'id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::className(), 'targetAttribute' => ['store_id' => 'id']],

            [['merchantName', 'storeName'], 'string', 'max' => 256],
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

            'id' => Yii::t('app', 'ID'),
            'merchant_id' => Yii::t('app', 'Esercente'),
            'store_id' => Yii::t('app', 'Negozio'),
            'appName' => Yii::t('app', 'Pos'),
            'description' => Yii::t('app', 'Descrizione'),
            'sin' => Yii::t('app', 'Sin'),

            'create_date' => Yii::t('app', 'Create Date'),
            'close_date' => Yii::t('app', 'Close Date'),
            'historical' => Yii::t('app', 'Historical'),
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
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\StoresQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::className(), ['id' => 'store_id']);
    }

    /**
     * Gets query for [[Possettings]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\PossettingsQuery
     */
    public function getPossettings()
    {
        return $this->hasOne(Possettings::className(), ['pos_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\PosQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\PosQuery(get_called_class());
    }
}
