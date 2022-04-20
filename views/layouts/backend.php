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
            'options' => ['class' => 'navbar navbar-expand-md navbar-dark bg-dark bg-gradient sticky-top'],
        ]);
        echo Nav::widget([
            'activateParents' => true,
            'options' => ['class' => 'navbar-nav'],
            'items' => [
                ['label' => 'Home', 'url' => ['site/index']],
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
    <div class="flex-shrink-0" id="body">
        <div class="container mt-3">
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'] ?? []]) ?>
            <?= FlashAlertWidget::widget() ?>
            <div class="row">
                <aside class="col-3 col-xl-2" id="sidebar">
                    <?= Nav::widget([
                        'activateParents' => true,
                        'options' => ['class' => 'flex-column'],
                        'items' => [
                            ['label' => 'First page', 'url' => ['backend/first/index']],
                            ['label' => 'Second page', 'url' => ['backend/second/index']],
                        ],
                    ]) ?>
                </aside>
                <main class="col-9 col-xl-10" id="main" role="main">
                    <?= $content ?>
                </main>
            </div>
        </div>
    </div>
    <footer class="footer mt-auto bg-secondary bg-gradient bg-gradient-light" id="footer">
        <div class="container py-2">
            <div class="row">
                <div class="col-12 col-md-6 offset-md-3">
                    <div class="text-light text-center">
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
