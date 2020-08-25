<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'checklists';

/* @var $model frontend\modules\checklist\models\ChecklistForm */

?>
<h3>Здесь отображатся созданные чек-листы</h3>

<div class="main-field">
    <ul class="collapsible checklists">

    </ul>
</div>
<div class="checklist-form-add">
    <!-- Modal Triggers -->
    <a class="waves-effect waves-light btn modal-trigger" href="#modal1">Добавить чек-лист</a>
    <a class="waves-effect waves-light btn modal-trigger" href="#modal2" style="margin-top: 40px;">Удалить все чек-листы</a>
    <!-- end Modal Triggers -->
    <!-- Modal Structure -->
    <div id="modal2" class="modal">
        <div class="modal-content">
            <div>Вы уверены, что хотите удалить все чек-листы? Их уже не удастся восстановить!</div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="btn waves-effect waves-green delete-all modal-close">Да</a>

            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Нет</a>
        </div>
    </div>
    <!-- end Modal Structure -->
    <!-- Modal Structure -->
    <div id="modal1" class="modal">
        <div class="checklistForm">
            <div class="modal-content">
                <h4>Введите название чек-листа</h4>
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'name')->label('Название') ?>

                <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary']) ?>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-green btn-flat">Закрыть</a>
            </div>
        </div>
    </div>
    <!-- end Modal Structure -->
</div>

