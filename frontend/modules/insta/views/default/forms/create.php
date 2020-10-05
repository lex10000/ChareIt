<?php

use frontend\modules\insta\models\forms\PostForm;
use yii\widgets\ActiveForm;
$model = new PostForm(Yii::$app->user->getId());
?>
<div class="create_post_form">
    <?php $form = ActiveForm::begin([
        'action' => '/insta/default/create',
    ]); ?>

    <?= $form
        ->field($model, 'picture', ['options' => ['class' => 'upload_picture']])
        ->fileInput(['accept' => 'image/*', 'class' => 'upload_picture__btn'])
        ->label('
            <i class="material-icons">attach_file</i>
            <span class="title">Добавить файл</span>')  ?>
    <?= $form->field($model, 'description')->textInput() ?>
    <input type="submit" class="btn purple" value="Создать">
    <a href="#!" class="modal-close waves-effect waves-green btn-flat">Закрыть</a>
    <?php ActiveForm::end(); ?>
</div>

