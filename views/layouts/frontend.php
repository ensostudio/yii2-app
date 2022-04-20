<?php

/**
 * @var yii\web\View $this
 * @var string $content
 */

use app\assets\AppAsset;
use app\web\widgets\FlashAlertWidget;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>
    <header id="header">
        <?php
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-expand-md navbar-dark bg-dark sticky-top',
            ],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => [
                ['label' => 'Home', 'url' => ['site/index']],
                ['label' => 'About us', 'url' => ['site/about']],
                ['label' => 'Contacts', 'url' => ['site/contact']],
                Yii::$app->user->isGuest
                    ? ['label' => 'Login', 'url' => ['account/login']]
                    : '<li>' . Html::beginForm(['account/logout'], 'post', ['class' => 'form-inline'])
                    . Html::submitButton(
                        'Logout (' . Yii::$app->user->identity->login . ')',
                        ['class' => 'btn btn-link logout']
                    ) . Html::endForm() . '</li>'
            ],
        ]);
        NavBar::end();
        ?>
    </header>

    <div class="flex-shrink-0 mt-auto" id="body">
        <div class="container">
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'] ?? []]) ?>
            <?= FlashAlertWidget::widget() ?>
            <main id="main" role="main">
                <?= $content ?>
            </main>
        </div>
    </div>

    <footer class="footer mt-auto bg-light bg-gradient-light" id="footer">
        <div class="container py-3">
            <div class="row">
                <div class="col-12 col-md-6 offset-md-3">
                    <div class="text-muted text-center">
                        &copy; <?= Yii::$app->name . ' ' . date('Y') ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>