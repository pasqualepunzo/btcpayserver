<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "merchants".
 *
 * @property int $id
 * @property string $piva
 * @property string $description
 * @property string $address
 * @property string $email
 * @property string $create_date
 * @property string|null $close_date
 * @property int $historical
 */
class Merchants extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'merchants';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['piva', 'description', 'address', 'email', 'create_date'], 'required'],
            [['create_date', 'close_date'], 'safe'],
            [['historical'], 'integer'],
            [['piva'], 'string', 'max' => 16],
            [['description', 'address', 'email'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'piva' => Yii::t('app', 'P.Iva'),
            'description' => Yii::t('app', 'Descrizione'),
            'address' => Yii::t('app', 'Indirizzo'),
            'email' => Yii::t('app', 'Email'),
            'create_date' => Yii::t('app', 'Create Date'),
            'close_date' => Yii::t('app', 'Close Date'),
            'historical' => Yii::t('app', 'Historical'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\MerchantsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\MerchantsQuery(get_called_class());
    }
}
