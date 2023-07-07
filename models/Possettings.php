<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "possettings".
 *
 * @property int $id
 * @property int $pos_id
 * @property string|null $sin
 * @property string|null $appName
 * @property string|null $title
 * @property string|null $description
 * @property string|null $template
 * @property string|null $defaultView
 * @property string|null $currency
 * @property int|null $showCustomAmount
 * @property int|null $showDiscount
 * @property int|null $enableTips
 * @property string|null $customAmountPayButtonText
 * @property string|null $fixedAmountPayButtonText
 * @property string|null $tipText
 * @property string|null $customCSSLink
 * @property string|null $embeddedCSS
 * @property string|null $notificationUrl
 * @property string|null $redirectUrl
 * @property int|null $redirectAutomatically
 * @property int|null $requiresRefundEmail
 * @property string|null $checkoutType
 * @property string|null $formId
 *
 * @property Pos $pos
 */
class Possettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'possettings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pos_id'], 'required'],
            [['pos_id', 'showCustomAmount', 'showDiscount', 'enableTips', 'redirectAutomatically', 'requiresRefundEmail'], 'integer'],
            [['template'], 'safe'],
            [['sin'], 'string', 'max' => 255],
            [['appName', 'title'], 'string', 'max' => 30],
            [['description', 'defaultView', 'currency', 'customAmountPayButtonText', 'fixedAmountPayButtonText', 'tipText', 'customCSSLink', 'embeddedCSS', 'notificationUrl', 'redirectUrl', 'checkoutType', 'formId'], 'string', 'max' => 512],
            [['pos_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pos::className(), 'targetAttribute' => ['pos_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pos_id' => Yii::t('app', 'Pos ID'),
            'sin' => Yii::t('app', 'Sin'),
            'appName' => Yii::t('app', 'App Name'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'template' => Yii::t('app', 'Template'),
            'defaultView' => Yii::t('app', 'Default View'),
            'currency' => Yii::t('app', 'Currency'),
            'showCustomAmount' => Yii::t('app', 'Show Custom Amount'),
            'showDiscount' => Yii::t('app', 'Show Discount'),
            'enableTips' => Yii::t('app', 'Enable Tips'),
            'customAmountPayButtonText' => Yii::t('app', 'Custom Amount Pay Button Text'),
            'fixedAmountPayButtonText' => Yii::t('app', 'Fixed Amount Pay Button Text'),
            'tipText' => Yii::t('app', 'Tip Text'),
            'customCSSLink' => Yii::t('app', 'Custom Css Link'),
            'embeddedCSS' => Yii::t('app', 'Embedded Css'),
            'notificationUrl' => Yii::t('app', 'Notification Url'),
            'redirectUrl' => Yii::t('app', 'Redirect Url'),
            'redirectAutomatically' => Yii::t('app', 'Redirect Automatically'),
            'requiresRefundEmail' => Yii::t('app', 'Requires Refund Email'),
            'checkoutType' => Yii::t('app', 'Checkout Type'),
            'formId' => Yii::t('app', 'Form ID'),
        ];
    }

    /**
     * Gets query for [[Pos]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\PosQuery
     */
    public function getPos()
    {
        return $this->hasOne(Pos::className(), ['id' => 'pos_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\PossettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\PossettingsQuery(get_called_class());
    }
}
