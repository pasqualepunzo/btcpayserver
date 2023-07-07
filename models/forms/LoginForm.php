<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Users;
use app\components\Log;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
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
            'password' => Yii::t('app', 'Password'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user) {
                $this->addError($attribute, Yii::t('app','Nome utente o password errati.'));
                return false;
            }

        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id ?? 'index'), Yii::t('app', 'User {user} logged in.', ['user' => $this->_user->username]));
            
            // effettua l'accesso dell'utente
            return Yii::$app->user->login($this->getUser(), 3600*24*30);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $search = Users::findByUsername($this->username);

            if (null !== $search && $search->validatePassword($this->password)){
                $this->_user = $search;
            }
        }

        return $this->_user;
    }

   
}
