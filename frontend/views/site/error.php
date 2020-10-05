<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="error-page">
    <img src="/img/error.png" alt="">
    <div>
        <h1><?= Html::encode($this->title) ?></h1>
        <div class=""><?= Html::encode($message) ?></div>
        <a href="<?= \yii\helpers\Url::toRoute('/') ?>">На главную</a>
    </div>
</div>

