<?php
/**  @var \frontend\modules\user\models\User $user ; */

use yii\widgets\ActiveForm;

?>
<div>
    <div><?= Yii::$app->session->getFlash('changeSettings') ?? null ?></div>
    <?php $form = ActiveForm::begin([]); ?>
    <?= $form->field($user, 'picture')->fileInput() ?>
    <?= $form->field($user, 'about')->textInput() ?>
    <input type="submit" class="btn" value="Создать">
    <?php ActiveForm::end(); ?>
</div>


