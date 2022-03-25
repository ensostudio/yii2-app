<?php

/**
 * This is the template for generating an action view file.
 *
 * @var \yii\web\View $this
 * @var \yii\gii\generators\form\Generator $generator
 */

use yii\helpers\Inflector;

$name = basename($generator->viewName);

echo "<?php\n";
?>

/**
 * Action "<?= $generator->viewName ?>".
 *
 * @return string|void
 */
public function action<?= Inflector::id2camel(trim($name, '_')) ?>()
{
    $model = new \<?= ltrim($generator->modelClass, '\\') ?>(<?= $generator->scenarioName ? "['scenario' => '$generator->scenarioName']" : '' ?>);
    if ($this->request->isPost) {
        if ($model->load($this->request->post()) && $model->validate()) {
            $model->save();
        }
    } else {
        $model->loadDefaultValues();
    }

    return $this->render('<?= $name ?>', ['model' => $model]);
}
