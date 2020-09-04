<?php
/* @var $model frontend\modules\insta\models\forms\PostForm*/
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<div><?= Yii::$app->session->getFlash('success')?></div>
<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'picture')->fileInput()?>
    <?= $form->field($model, 'description')?>
    <?= Html::submitButton('create')?>
<?php ActiveForm::end();?>

