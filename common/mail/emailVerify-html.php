<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user \frontend\modules\user\models\User */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['user/default/verify-email', 'token' => $user->verification_token]);
?>
<div class="verify-email">
    <p>Здравствуйте, <?= Html::encode($user->username) ?>,</p>

    <p>Для подтверждения регистрации пожалуйста перейдите по этой ссылке:</p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>

    <br>
    <br>
    <p>После подтверждения регистрации, вы можете изменить настройки профиля (аватарку, информацио о себе, сменить пароль)
    в разделе "настройки"</p>
    <p>P.S. Найдите в друзьях меня, lex10000, чтобы посмотреть, как работает модуль "друзья"</p>
</div>
