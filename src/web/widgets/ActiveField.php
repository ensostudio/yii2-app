<?php

namespace app\models;

use yii\helpers\ArrayHelper;

/**
 * @inheritDoc
 */
class ActiveField extends \yii\bootstrap5\ActiveField
{
    /**
     * @inheritDoc
     */
    public $errorOptions = ['class' => 'invalid-tooltip'];
    /**
     * @inheritDoc
     */
    public $horizontalCssClasses = [
        'offset' => ['col', 'col-md-9', 'col-lg-10', 'offset-md-3', 'offset-lg-2'],
        'label' => ['col', 'col-md-3', 'col-lg-2'],
        'wrapper' => ['col', 'col-md-9', 'col-lg-10'],
        'error' => ['invalid-tooltip'],
        'hint' => ['form-text', 'text-muted'],
        'field' => ['row', 'mb-2'],
    ];

    /**
     * @var array The options to pass to `self::captcha()`
     */
    public array $captchaOptions = [
        'template' => '<div class="input-group has-validation">{image}{input}</div>',
        'input' => ['class' => ['form-control'], 'size' => 6, 'required' => true],
        'image' => ['class' => ['input-group-text', 'input-group-image']],
    ];

    /**
     * Renders a number input.
     *
     * @param array $options The tag options in terms of name-value pairs. These will be rendered as the attributes of
     * the resulting tag. The values will be HTML-encoded using `Html::encode()`. Tag attributes: min, max, size, step.
     * @return $this
     */
    public function numberInput(array $options = []): self
    {
        // @todo Html::addCssClass($options, ['widget' => 'form-control']);
        $options += ['size' => 10];

        return $this->input('number', $options);
    }

    /**
     * Renders a URL input.
     *
     * @param array $options The tag options in terms of name-value pairs. These will be rendered as the attributes of
     * the resulting tag. The values will be HTML-encoded using `Html::encode()`. Tag attributes: minlength, maxlength,
     * size, pattern.
     * @return $this
     */
    public function urlInput(array $options = []): self
    {
        return $this->input('url', $options);
    }

    /**
     * Renders a email input.
     *
     * @param array $options The tag options in terms of name-value pairs. These will be rendered as the attributes of
     * the resulting tag. The values will be HTML-encoded using `Html::encode()`. Tag attributes: minlength, maxlength,
     * size.
     * @return $this
     */
    public function emailInput(array $options = []): self
    {
        $options += ['size' => 30];

        return $this->input('email', $options);
    }

    /**
     * Renders a telephone input.
     *
     * @param array $options The tag options in terms of name-value pairs. These will be rendered as the attributes of
     * the resulting tag. The values will be HTML-encoded using `Html::encode()`. Tag attributes: minlength, maxlength,
     * size, pattern.
     * @return $this
     */
    public function phoneInput(array $options = []): self
    {
        if (empty($options['size'])) {
            $options['size'] = 15;
        }
        if (empty($options['minlength'])) {
            $options['minlength'] = 10;
        }
        if (empty($options['maxlength'])) {
            $options['maxlength'] = 15;
        }

        return $this->input('tel', $options);
    }

    /**
     * Renders a date input.
     *
     * @param array $options The tag options in terms of name-value pairs. These will be rendered as the attributes of
     * the resulting tag. The values will be HTML-encoded using `Html::encode()`. Tag attributes: min, max, size.
     * @return $this
     * @throws \yii\base\InvalidConfigException
     */
    public function dateInput(array $options = []): self
    {
        if (!empty($options['min']) && !is_string($options['min'])) {
            $options['min'] = \Yii::$app->formatter->asDate($options['min'], 'yyyy-MM-dd');
        }
        if (!empty($options['max']) && !is_string($options['max'])) {
            $options['max'] = \Yii::$app->formatter->asDate($options['max'], 'yyyy-MM-dd');
        }
        $options += ['size' => 10, 'maxlength' => 10];

        return $this->input('date', $options);
    }

    /**
     * Renders a time input.
     *
     * @param array $options The tag options in terms of name-value pairs. These will be rendered as the attributes of
     * the resulting tag. The values will be HTML-encoded using `Html::encode()`. Tag attributes: min, max, size.
     * @return $this
     * @throws \yii\base\InvalidConfigException
     */
    public function timeInput(array $options = []): self
    {
        if (isset($options['min']) && !is_string($options['min'])) {
            $options['min'] = \Yii::$app->formatter->asDate($options['min'], 'HH:mm');
        }
        if (isset($options['max']) && !is_string($options['max'])) {
            $options['max'] = \Yii::$app->formatter->asDate($options['max'], 'HH:mm');
        }
        $options += ['size' => 10, 'maxlength' => 5];

        return $this->input('time', $options);
    }

    /**
     * Renders a local date&time input.
     *
     * @param array $options The tag options in terms of name-value pairs. These will be rendered as the attributes of
     * the resulting tag. The values will be HTML-encoded using `Html::encode()`. Tag attributes: min, max, size.
     * @return $this
     * @throws \yii\base\InvalidConfigException
     */
    public function datetimeInput(array $options = []): self
    {
        if (!empty($options['min']) && !is_string($options['min'])) {
            $options['min'] = \Yii::$app->formatter->asDatetime($options['min'], 'yyyy-MM-dd HH:mm');
        }
        if (!empty($options['max']) && !is_string($options['max'])) {
            $options['max'] = \Yii::$app->formatter->asDatetime($options['max'], 'yyyy-MM-dd HH:mm');
        }
        $options += ['size' => 16, 'maxlength' => 16];

        return $this->input('datetime-local', $options);
    }

    /**
     * Renders a select with options. Alias of `self::dropDownList()`.
     *
     * @param array $items the option data items. The array keys are option values, and the array values are the
     * corresponding option labels. The array can also be nested (i.e. some array values are arrays too). For each
     * sub-array, an option group will be generated whose label is the key associated with the sub-array.
     * @param array $options The tag options in terms of name-value pairs. These will be rendered as the attributes of
     * the resulting tag. The values will be HTML-encoded using `Html::encode()`. Tag attributes: min, max, size.
     * @return $this
     */
    public function select(array $items, array $options = []): self
    {
        return $this->dropDownList($items, $options);
    }

    /**
     * Renders a CAPTCHA input.
     *
     * @param array $options The tag options in terms of name-value pairs. These will be rendered as the attributes of
     * the resulting tag. The values will be HTML-encoded using `Html::encode()`. Widget options:
     * `image`(or `imageOptions`), `input`(or `options`) and `action`(or `captchaAction`)
     * @return $this
     * @throws \Exception
     * @todo import scss https://codepen.io/WinterSilence/pen/xxpVxgp
     */
    public function captcha(array $options = []): self
    {
        $options = array_merge_recursive($this->captchaOptions, $options);
        $options['imageOptions'] = ArrayHelper::remove($options, 'image', []);
        $options['options'] = ArrayHelper::remove($options, 'input', []);
        if (isset($options['action'])) {
            $options['captchaAction'] = ArrayHelper::remove($options, 'action');
        }
        $class = ArrayHelper::remove($options, 'class', \yii\captcha\Captcha::class);

        return $this->widget($class, $options);
    }
}
