<?php
/** @var $changePasswordModel \frontend\modules\user\models\ResetPasswordForm */

use yii\widgets\ActiveForm;

?>

<div class="site-change-password">
    <p>Изменить пароль</p>
    <?= Yii::$app->session->getFlash('changePassword') ?? null ?>
    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
    <?= $form->field($changePasswordModel, 'password')->passwordInput(['placeholder' => 'минимум 6 символов'])?>
    <?= $form->field($changePasswordModel, 'password_repeat')->passwordInput() ?>
    <input type="submit" class="btn" value="Сохранить новый пароль"/>
    <?php ActiveForm::end(); ?>
</div>