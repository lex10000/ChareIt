<?php
/** @var $changePasswordModel \frontend\modules\user\models\ResetPasswordForm  */
/** @var $this yii\web\View  */
/** @var $user \frontend\modules\user\models\User;  */

?>
<div class="settings">
    <div>
        <?= $this->render('change_password_view', [
            'changePasswordModel' => $changePasswordModel
        ])?>
    </div>
    <div>
        <?= $this->render('delete_user_view', [])?>
    </div>
    <div>
        <?= $this->render('user_settings_view', [
            'user' => $user
        ])?>
    </div>
</div>

