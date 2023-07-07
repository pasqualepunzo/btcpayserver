<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $oauth_provider
 * @property string $oauth_uid
 * @property string $authKey
 * @property string $accessToken
 * @property string $picture
 * @property string $privilege_id
 * @property int $is_active
 * @property int $merchant_id
 * @property int $store_id
 *
 * @property Privileges $privilege
 *
 * @property Auth[] $auths
 */
class Users extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface

{
    const STATUS_INSERTED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'first_name', 'last_name', 'email', 'oauth_provider', 'oauth_uid', 'authKey', 'accessToken', 'picture', 'privilege_id'], 'required'],
            [['privilege_id', 'is_active', 'merchant_id', 'store_id'], 'integer'],
            [['username', 'authKey', 'first_name', 'last_name', 'email'], 'string', 'max' => 256],
            [['oauth_provider'], 'string', 'max' => 20],
            [['oauth_uid'], 'string', 'max' => 128],
            [['picture'], 'string', 'max' => 512],
            [['accessToken'], 'string', 'max' => 2048],

            [['privilege_id'], 'exist', 'skipOnError' => true, 'targetClass' => Privileges::className(), 'targetAttribute' => ['privilege_id' => 'id']],
            [['merchant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Merchants::className(), 'targetAttribute' => ['merchant_id' => 'id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::className(), 'targetAttribute' => ['store_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Nome utente'),
            'oauth_provider' => Yii::t('app', 'OAuth Provider'),
            'oauth_uid' => Yii::t('app', 'OAuth ID'),
            'authKey' => Yii::t('app', 'Auth Key'),
            'accessToken' => Yii::t('app', 'Access Token'),
            'first_name' => Yii::t('app', 'Nome'),
            'last_name' => Yii::t('app', 'Cognome'),
            'email' => Yii::t('app', 'Email'),
            'picture' => Yii::t('app', 'Picture'),
            'privilege_id' => Yii::t('app', 'Profilo'),
            'is_active' => Yii::t('app', 'Abilitato'),
            'merchant_id' => Yii::t('app', 'Esercente'),
            'store_id' => Yii::t('app', 'Negozio'),
        ];
    }

    /**
     * Gets query for [[Auths]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\AuthQuery
     */
    public function getAuths()
    {
        return $this->hasMany(Auth::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Privilege]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\PrivilegesQuery
     */
    public function getPrivilege()
    {
        return $this->hasOne(Privileges::className(), ['id' => 'privilege_id']);
    }

    /**
     * Gets query for [[Merchants]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\MerchantsQuery
     */
    public function getMerchant()
    {
        return $this->hasOne(Merchants::className(), ['id' => 'merchant_id']);
    }

    /**
     * Gets query for [[Stores]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\StoresQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::className(), ['id' => 'store_id']);
    }

    

    /**
     * {@inheritdoc}
     * @return \app\models\query\UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\UsersQuery(get_called_class());
    }

    
    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    

    private function setAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString(60);
    }

    private function setUid()
    {
        $this->oauth_uid = Yii::$app->security->generateRandomString(60);
    }

    public function activate()
    {
        $this->setUid();
        return $this->save();
    }
    
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(['accessToken' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::findOne(['username' => $username]);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {

        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }
}
