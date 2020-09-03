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
                RealInsta - новый взгляд на популярность в социальных сетях. Если ты еще не в теме, обязательно прочти
                <a href="#">этот раздел</a>
            </p>
            <div class="welcome__login-form welcome__auth-form">
                <?php $form = ActiveForm::begin([
                    'action' => '/user/default/login',
                ]) ?>
                <? if(Yii::$app->session->hasFlash('danger')): ?>
                <div class="danger_message"><?= Yii::$app->session->getFlash('danger') ?></div>
                <? endif; ?>
                <div>
                    <?= $form->field($model, 'username', ['options' => ['class' => 'input-field']])->textInput()->label('логин')?>
                    <?= $form->field($model, 'password', ['options' => ['class' => 'input-field']])->passwordInput()->label('пароль') ?>
                    <button class="btn waves-effect waves-light purple" type="submit" name="action">Войти</button>
                </div>
                <?php ActiveForm::end() ?>
                <a href="#!" class="get_signup_form">Зарегистрироваться</a>
            </div>
        </div>
    </div>
</div>
