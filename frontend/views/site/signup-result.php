<?php
/* @var $this \yii\web\View */
?>
<div class="about">

    <a href="<?= \yii\helpers\Url::to('/') ?>" class="back_link">
        <i class="material-icons">arrow_back</i>Назад на главную
    </a>
    <? if (Yii::$app->session->hasFlash('signup-success')): ?>
        <div class="about-items">
            <div class="about-item">
                <div class="about-item__header">Поздравляем!</div>
                <p>
                    <?= Yii::$app->session->getFlash('signup-success') ?>
                </p>
            </div>
        </div>
    <? elseif (Yii::$app->session->hasFlash('signup-fail')): ?>
        <div class="about-items">
            <div class="about-item">
                <div class="about-item__header">Ошибка:(</div>
                <p>
                    <?= Yii::$app->session->getFlash('signup-fail'); ?>
                </p>
            </div>
        </div>
    <? endif; ?>
</div>
