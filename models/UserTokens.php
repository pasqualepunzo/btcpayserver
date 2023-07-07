<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_tokens".
 *
 * @property int $id
 * @property int $pos_id
 * @property string $token
 * @property int $created_at
 *
 * @property Pos $pos
 */
class UserTokens extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_tokens';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pos_id', 'token', 'created_at'], 'required'],
            ['pos_id', 'unique'],
            [['pos_id', 'created_at'], 'integer'],
            [['token'], 'string', 'max' => 255],
            [['pos_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pos::class, 'targetAttribute' => ['pos_id' => 'id']],
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
            'token' => Yii::t('app', 'Token'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
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
     * {@inheritdoc}
     * @return \app\models\query\UserTokensQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\UserTokensQuery(get_called_class());
    }
}
