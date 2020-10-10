<?php

use frontend\modules\chareit\models\forms\PostForm;
use yii\widgets\ActiveForm;

$model = new PostForm(Yii::$app->user->getId());
?>
<!-- Modal Structure -->
<div id="modal1" class="modal create_post">
    <div class="modal-content">
        <div class="create_post_form">
            <?php $form = ActiveForm::begin(['action' => '/chareit/default/create']); ?>
            <?= $form
                ->field($model, 'picture', ['options' => ['class' => 'upload_picture']])
                ->fileInput(['accept' => 'image/*', 'class' => 'upload_picture__btn'])
                ->label('<i class="material-icons">attach_file</i><span class="title">Добавить файл</span>') ?>
            <?= $form->field($model, 'description')->textInput() ?>
            <input type="submit" class="btn" value="Создать">
            <a href="#!" class="modal-close waves-effect waves-yellow btn-flat">Закрыть</a>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<!-- end Modal Structure -->


