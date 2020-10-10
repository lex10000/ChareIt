<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user \frontend\modules\user\models\User */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['user/default/verify-email', 'token' => $user->verification_token]);
?>
<div class="verify-email">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>Для подтверждения регистрации пожалуйста перейдите по этой ссылке:</p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>
</div>
