<?php

namespace app\models;

use yii\base\Model;
use yii\web\IdentityInterface;

/**
 *
 */
class User extends Model implements IdentityInterface
{
    /**
     * @var int
     */
    public int $id;
    /**
     * @var string
     */
    public string $login;
    /**
     * @var string
     */
    public string $password;
    /**
     * @var string
     */
    public string $authKey;
    /**
     * @var string
     */
    public string $accessToken;

    private static array $users = [
        100 => [
            'id' => 100,
            'login' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        101 => [
            'id' => 101,
            'login' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];

    /**
     * @inheritDoc
     */
    public static function findIdentity($id): ?self
    {
        return isset(self::$users[$id]) ? new self(self::$users[$id]) : null;
    }

    /**
     * @inheritDoc
     */
    public static function findIdentityByAccessToken($token, $type = null): ?self
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new self($user);
            }
        }

        return null;
    }

    /**
     * Finds user by [[login]].
     *
     * @param string $login the user login
     * @return self|null
     */
    public static function findByLogin(string $login): ?self
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['login'], $login) === 0) {
                return new self($user);
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey(): ?string
    {
        return $this->authKey;
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates [[password]].
     *
     * @param string $password the password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return $this->password === $password;
    }
}
