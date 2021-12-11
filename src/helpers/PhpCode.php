<?php

namespace ensostudio\helpers;

/**
 * Wrapper for inline PHP code, examples: `yii\web\Application::className()`, `Yii::t('app', 'items')`.
 */
class PhpCode
{
    /**
     * @var string the PHP code
     */
    private $code;

    /**
     * @param string|Stringable $code
     * @return void
     */
    public function __construct($code)
    {
        $this->code = (string) $code;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }
}
