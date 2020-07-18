<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \frontend\models\User */
$this->title = 'Welcome page';

?>
<div class="welcome__page">
    <div class="welcome welcome__left">
        <img src="/img/logo.png" alt="logo">
    </div>
    <div class="welcome welcome__right">
        <header class="welcome__header">
            <ul>
                <li><a href="#">ПОДРОБНЕЕ О ПРОЕКТЕ</a></li>
                <li><a href="#">ДЛЯ СПОНСОРОВ</a></li>
            </ul>
        </header>
        <div class="welcome__promo" >
            <h2>Добро пожаловать!</h2>
            <p>
                RealInsta - новый взгляд на популярность в социальных сетях. Если ты еще не в теме, обязательно прочти <a
                        href="#">этот раздел</a>
            </p>
            <?php \yii\widgets\Pjax::begin(['enablePushState' => false]) ?>
            <div class="row welcome__login-form">
                <?php $form = ActiveForm::begin([
                    'action' => '/user/default/login',
                    'options' => ['class' => 'col s12'],
                ]) ?>
                <div class="row">
                    <?= $form->field($model, 'username', ['options' => ['class' => 'input-field col s12 '], 'enableAjaxValidation' => true])->textInput()->label('логин')?>
                    <?= $form->field($model, 'password', ['options' => ['class' => 'input-field col s12 ']])->passwordInput()->label('пароль') ?>
                    <button class="btn waves-effect waves-light purple" type="submit" name="action">Войти</button>
                </div>
                <?php ActiveForm::end() ?>
                <a href="<?= Url::to('/user/default/signup')?>">Зарегистрироваться</a>
            </div>
            <?php \yii\widgets\Pjax::end() ?>
        </div>
    </div>
</div>
