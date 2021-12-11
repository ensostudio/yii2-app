<?php

namespace app\widgets;

use yii\bootstrap5\ActiveForm;

use yii\bootstrap5\Html;
use yii\validators\NumberValidator;
use yii\validators\RangeValidator;

/**
 * @inheritDoc
 * @property-read string $inputId `id` attribute of field tag
 */
class ActiveField extends \yii\bootstrap5\ActiveField
{
    /**
     * @var string the template for custom checkbox (switch) in default layout
     */
    public $switchTemplate = "<div class=\"form-check form-switch\">\n{input}\n{label}\n{error}\n{hint}\n</div>";
    /**
     * @var string the template for custom checkbox (switch) in horizontal layout
     */
    public $switchHorizontalTemplate = "{beginWrapper}\n"
        . "<div class=\"form-check form-switch\">\n{input}\n{label}\n{error}\n{hint}\n</div>\n"
        . "{endWrapper}";

    

    /**
     * Renders a switch (custom checkbox).
     *
     * @param array $options the tag options in terms of name-value pairs
     * @return $this
     * @see https://getbootstrap.com/docs/5.1/forms/checks-radios/#switches
     */
    public function switchbox(array $options = []): self
    {
        if (!isset($options['template'])) {
            $options['template'] = $this->form->layout === ActiveForm::LAYOUT_HORIZONTAL
                ? $this->switchHorizontalTemplate
                : $this->switchTemplate;
        }
        $this->addRoleAttributes($options, 'switch');

        return $this->checkbox($options);
    }

    /**
     * Renders a range (custom input).
     *
     * @param array $options the tag options in terms of name-value pairs:
     *     - 'min': min. value, by default, 0
     *     - 'max': max. value, by default, 100
     *     - 'step': range step, by default, 1
     * @return $this
     * @see https://getbootstrap.com/docs/5.1/forms/range/
     */
    public function rangeInput(array $options = []): self
    {
        if (!isset($options['step'], $options['min'], $options['max'])) {
            $validators = $this->model->getActiveValidators($this->attribute);
            foreach ($validators as $validator) {
                if ($validator instanceof NumberValidator) {
                    if (!isset($options['step'])) {
                        $options['step'] = $validator->integerOnly ? 1 : 0.1;
                    }
                    if (!isset($options['min'])) {
                        $options['min'] = $validator->min;
                    }
                    if (!isset($options['max'])) {
                        $options['max'] = $validator->max;
                    }
                }
                if ($validator instanceof RangeValidator && is_array($validator->range)) {
                    if (!isset($options['min'])) {
                        $options['min'] = reset($validator->range);
                    }
                    if (!isset($options['max'])) {
                        $options['max'] = end($validator->range);
                    }
                }
            }
        }

        Html::removeCssClass($options, 'form-control');
        Html::addCssClass($options, ['widget' => 'form-range']);

        return $this->input('range', $options);
    }

    /**
     * Renders a color picker (custom input).
     *
     * @param array $options the tag options in terms of name-value pairs
     * @return $this
     * @see https://getbootstrap.com/docs/5.1/forms/form-control/#color
     */
    public function colorInput(array $options = []): self
    {
        Html::addCssClass($options, ['widget' => 'form-control form-control-color']);

        return $this->input('color', $options);
    }
}
