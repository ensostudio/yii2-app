<?php

// Run console command `yii message /path/to/module/messages/config.php` in your application
return [
    'sourcePath' => dirname(__DIR__),
    'languages' => ['ru'],
    'translator' => ['Yii::t', '\Yii::t', '$this->module->i18n'],
    'sort' => true,
    'removeUnused' => true,
    'markUnused' => false,
    'only' => ['*.php', '*.phtml', '*.tpl'],
    'except' => ['.*', '/.*', '/messages', '/tests'],
    'format' => 'php',
    'messagePath' => __DIR__,
    'overwrite' => true,
    'phpFileHeader' => '',
    'phpDocBlock' => null,
    'ignoreCategories' => ['yii', 'app'],
    /*
    // for saving messages to database
    'format' => 'db',
    'sourceMessageTable' => '{{%i18n_source_messages}}',
    'messageTable' => '{{%i18n_messages}}',
    // for saving messages to gettext po files
    'format' => 'po',
    'catalog' => 'messages',
    */
];
