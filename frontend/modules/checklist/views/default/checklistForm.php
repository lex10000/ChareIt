<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\checklist\models\ChecklistForm */
?>
<div class="checklistForm">

    <?php $form = ActiveForm::begin([
        'options' => [
            'class' => 'forma1',
            'data' => [
                'pjax' => 1,
            ],
        ]
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div><!-- checklistForm -->
