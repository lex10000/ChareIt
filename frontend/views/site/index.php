<?php

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \frontend\modules\user\models\LoginForm*/

?>
<div class="welcome__page">
    <div class="welcome__left">
        <div class="welcome__logo">
            <img src="/img/logo.png" alt="logo">
        </div>
        <a href="#welcome__promo" class="arrow_down anchor"><i class="material-icons white-text">arrow_downward</i></a>
    </div>
    <div class="welcome__right">
        <header class="welcome__header">
            <ul>
                <li><a href="/about">ПОДРОБНЕЕ О ПРОЕКТЕ</a></li>
            </ul>
        </header>
        <div class="welcome__promo" id="welcome__promo">
            <h2>Добро пожаловать!</h2>
            <p>
                ChareIt - новый взгляд на популярность в социальных сетях. Если ты еще не в теме, обязательно прочти
                <a href="/about">этот раздел</a>
            </p>
            <div class="welcome__login-form welcome__auth-form">
                <?php $form = ActiveForm::begin([
                    'action' => '/user/default/login',
                ]) ?>

                <? if(Yii::$app->session->hasFlash('invalid_login')):?>
                <div class="danger_message"><?= Yii::$app->session->getFlash('invalid_login')?></div>
                <? endif;?>
                <div>
                    <?= $form->field($model, 'username')->textInput(['autofocus' => true])?>
                    <?= $form->field($model, 'password')->passwordInput()?>
                    <button class="btn waves-effect waves-light purple" type="submit" name="action">Войти</button>
                </div>
                <?php ActiveForm::end() ?>
                <a href="#!" class="get_signup_form">Зарегистрироваться</a>
            </div>
        </div>
    </div>
</div>
