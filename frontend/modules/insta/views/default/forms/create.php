<?php

use frontend\modules\insta\models\forms\PostForm;
use yii\widgets\ActiveForm;
$model = new PostForm(Yii::$app->user->getId());
?>
<h3>Добавьте фотографию</h3>
<div class="create_post_form">
    <?php $form = ActiveForm::begin([
        'action' => '/insta/default/create',
        'options' => [
            'class' => 'create_post',
        ]
    ]); ?>
    <?= $form->field($model, 'picture')->fileInput(['accept' => 'image/*']) ?>
    <?= $form->field($model, 'description')->textInput() ?>
    <input type="submit" class="btn" value="Создать">
    <a href="#!" class="modal-close waves-effect waves-green btn-flat">Закрыть</a>
    <?php ActiveForm::end(); ?>
</div>

