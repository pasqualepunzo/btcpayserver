<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Users;
use app\components\Log;


/**
 * Model representing Signup Form.
 */
class SignupForm extends Model
{
    const PRIVILEGE_ADMINISTRATOR_ID = 1; // privilege_id -> webmaster
    const PRIVILEGE_USER_ID = 2; // USER

    public $username;
    public $password;
    public $first_name;
    public $last_name;
    public $email;

    // holds the password confirmation word
    public $repeat_password;


    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'string', 'min' => 6, 'max' => 255],
            ['username', 'match',  'not' => true,
                // we do not want to allow users to pick one of spam/bad usernames
                'pattern' => '/\b('.Yii::$app->params['user.spamNames'].')\b/i',
                'message' => Yii::t('app', 'Non puoi usare questo nome utente.')],
            ['username', 'unique', 'targetClass' => '\app\models\Users',
                 'message' => Yii::t('app', 'Il nome utente inserito è già stato utilizzato.')],

            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\Users',
                'message' => Yii::t('app', 'La mail inserita è già stata utilizzata.')
            ],
            [['password', 'username', 'first_name', 'last_name', 'email', 'repeat_password'], 'required'],
            ['password', 'string', 'min' => 8],

            ['repeat_password', 'compare', 'compareAttribute'=>'password'],

        ];
    }

   
    /**
     * Returns the attribute labels.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Nome utente'),
            'first_name' => Yii::t('app', 'Nome'),
            'last_name' => Yii::t('app', 'Cognome'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'repeat_password' => Yii::t('app', 'Conferma password'),
        ];
    }

    /**
     * Signs up the user.
     * If scenario is set to "rna" (registration needs activation), this means
     * that user need to activate his account using email confirmation method.
     *
     * @return User|null The saved model or null if saving fails.
     */
    public function signup()
    {
        // echo "<pre>".print_r($_POST,true)."</pre>";
		// exit;

        if ($this->validate()) {
            $privilegio = self::PRIVILEGE_USER_ID;
            $test_privilege = Users::findOne(1);
            if (null === $test_privilege) {
                $privilegio = self::PRIVILEGE_ADMINISTRATOR_ID;
            }

            $user = new Users([
                'username' => $this->username,
                'password' => \Yii::$app->getSecurity()->generatePasswordHash($this->password),
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'oauth_provider' => 'self',
                'oauth_uid' => \Yii::$app->security->generateRandomString(),
                'authKey' => \Yii::$app->security->generateRandomString(),
                'accessToken' => \Yii::$app->security->generateRandomString(),
                'picture' => 'bundles/site/images/anonymous.png',
                'privilege_id' => $privilegio,
                'is_active' => (null === $test_privilege) ? 1 : 0,
            ]);

            if ($user->save()) {
                Log::save(Yii::$app->controller->id, (Yii::$app->action->id ?? 'index'), Yii::t('app', 'User {user} signed up.', ['user' => $this->username]));
                
                return $user;
            } else {
                $message = Yii::t('app', 'Unable to save {client} for new account {account}:</br></br>{errors}.', [
                    'client' => 'self',
                    'errors' => $user->getErrors(),
                    'account' => $this->username,
                ]);
                Yii::$app->getSession()->setFlash('error', $message);
                Log::save(Yii::$app->controller->id, (Yii::$app->action->id ?? 'index'), $message);
                return null;
            }

        }
        return false;
    }

}
