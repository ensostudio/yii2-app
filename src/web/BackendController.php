<?php

namespace app\web;

use yii\filters\AccessControl;
use yii\filters\AccessRule;

/**
 * Backend-end Web controller.
 *
 * Note: controller actions are available only to registered users assigned to `admin` role.
 *
 * @property-read array[]|AccessRule[] $accessRules The rules to check user access, {@see static::getAccessRules()}.
 */
class BackendController extends FrontendController
{
    /**
     * @inheritDoc
     */
    public $layout = 'backend';

    /**
     * Returns The rules to check user access.
     *
     * By default, `allow` property/key is `true`.
     *
     * @return array[]|AccessRule[]
     * @see AccessControl::$rules
     */
    public function getAccessRules(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        $rules = [
            new AccessRule(['allow' => true, 'roles' => ['@', 'admin']])
        ];
        // Normalizes access rules
        foreach ($this->getAccessRules() as $rule) {
            if (is_array($rule)) {
                $rule = new AccessRule($rule);
            }
            if ($rule instanceof AccessRule && !isset($rule->allow)) {
                $rule->allow = true;
            }
            $rules[] = $rule;
        }

        return ['AccessControl' => ['class' => AccessControl::class, 'rules' => $rules]] + parent::behaviors();
    }
}
