<?php

use yii\widgets\ActiveForm;

?>

<div class="delete_user">
    <p>Если вы решили удалить аккаунт, то все ваши фотографии так же будут удалены!</p>
    <?php $form = ActiveForm::begin([
        'action' => '/user/default/delete-user',
        'id' => 'delete-user-form'
    ]); ?>
    <input type="submit" class="btn" value="Удалить аккаунт"/>

    <?php ActiveForm::end(); ?>
</div>
