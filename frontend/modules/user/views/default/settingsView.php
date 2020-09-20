<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var \frontend\modules\user\models\ResetPasswordForm $changePasswordModel */

?>
<div class="settings">
    <div class="site-change-password">
        <p>Изменить пароль</p>

        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
        <?= $form->field($changePasswordModel, 'password')
            ->passwordInput(['autofocus' => true, 'placeholder' => 'введите пароль (мимнимум 6 символов)']) ?>
        <?= $form->field($changePasswordModel, 'password_repeat')
            ->passwordInput(['placeholder' => 'повторите пароль']) ?>

        <input type="submit" class="btn" value="Соохранить новый пароль"/>

        <?php ActiveForm::end(); ?>
        <?= Yii::$app->session->getFlash('changePassword') ?? null ?>
    </div>
    <div class="delete_user">
        <p>Если вы решили удалить аккаунт, то все ваши фотографии так же будут удалены!</p>
        <?php $form = ActiveForm::begin([
                'action' => '/user/default/delete-user',
                'id' => 'delete-user-form'
        ]); ?>
        <input type="submit" class="btn" value="Удалить аккаунт"/>

        <?php ActiveForm::end(); ?>
    </div>
</div>

