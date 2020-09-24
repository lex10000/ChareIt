<?php
/** @var $changePasswordModel \frontend\modules\user\models\ResetPasswordForm */

use yii\widgets\ActiveForm;

?>

<div class="site-change-password">
    <p>Изменить пароль</p>

    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
    <?= $form->field($changePasswordModel, 'password')
        ->passwordInput(['placeholder' => 'введите пароль (минимум 6 символов)']) ?>
    <?= $form->field($changePasswordModel, 'password_repeat')
        ->passwordInput(['placeholder' => 'повторите пароль']) ?>

    <input type="submit" class="btn" value="Соохранить новый пароль"/>

    <?php ActiveForm::end(); ?>
    <?= Yii::$app->session->getFlash('changePassword') ?? null ?>
</div>