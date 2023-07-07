<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "privileges".
 *
 * @property int $id
 * @property string $description
 * @property int $level
 * @property string $cognito_code
 */
class Privileges extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'privileges';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'level'], 'required'],
            [['level'], 'integer'],
            [['description'], 'string', 'max' => 50],
            // [['cognito_code'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'description' => Yii::t('app', 'Descrizione'),
            'level' => Yii::t('app', 'Livello'),
            'cognito_code' => Yii::t('app', 'Codice privilegio'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\PrivilegesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\PrivilegesQuery(get_called_class());
    }
}
