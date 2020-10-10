<?php
/**  @var \frontend\modules\user\models\User $user ; */

use yii\widgets\ActiveForm;

?>
<div>
    <div><?= Yii::$app->session->getFlash('changeSettings') ?? null ?></div>
    <?php $form = ActiveForm::begin([]); ?>
    <?= $form->field($user, 'picture', ['options' => ['class' => 'upload_picture']])
        ->fileInput(['accept' => 'image/*', 'class' => 'upload_picture__btn'])
        ->label('<i class="material-icons">attach_file</i><span class="title">Добавить файл</span>')?>
    <?= $form->field($user, 'about')->textInput() ?>
    <input type="submit" class="btn" value="Сохранить изменения">
    <?php ActiveForm::end(); ?>
</div>


