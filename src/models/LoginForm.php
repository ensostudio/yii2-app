<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * The model behind the login form.
 *
 * @property-read User|null $user the current user
 */
class LoginForm extends Model
{
    /**
     * @var string The user login
     */
    public string $login;
    /**
     * @var string The user password
     */
    public string $password;
    /**
     * @var bool The checkbox "remember me"
     */
    public bool $rememberMe = true;

    /**
     * @var User|null The current user
     */
    private ?User $user;


    /**
     * @inheritDoc
     * @return array the validation rules
     */
    public function rules(): array
    {
        return [
            [['login', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the [[password]].
     *
     * @param string $attribute the attribute currently being validated
     */
    public function validatePassword(string $attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if ($user === null || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided [[login]] and [[password]].
     *
     * @return bool whether the user is logged in successfully
     */
    public function login(): bool
    {
        if ($this->validate()) {
            $user = $this->getUser();

            return !($user === null) && Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 : 0);
        }

        return false;
    }

    /**
     * Finds user by [[login]].
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->user === null) {
            $this->user = User::findByLogin($this->login);
        }

        return $this->user;
    }
}
