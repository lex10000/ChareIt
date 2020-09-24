<?php
/* @var $model frontend\modules\insta\models\forms\PostForm */

use yii\widgets\ActiveForm;

?>
<div><?= Yii::$app->session->getFlash('success') ?></div>
<?php $form = ActiveForm::begin([
    'options' => [
        'class' => 'create_post',
    ]
]); ?>
<div class="file-field input-field">
    <div class="btn">
        <span>Добавить файл</span>
        <?= $form->field($model, 'picture')->fileInput() ?>
    </div>
    <div class="file-path-wrapper">
        <input class="file-path validate" type="text">
    </div>
</div>
<?= $form->field($model, 'description')->textInput() ?>
<input type="submit" class="btn" value="Создать">
<?php ActiveForm::end(); ?>

