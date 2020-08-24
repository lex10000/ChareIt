<?php
/* @var $checklist_options array */
/* @var $checklist_id integer */

/* @var $checklist_items \frontend\modules\checklist\models\ChecklistItems */

use yii\widgets\ActiveForm;

?>

<div class="checklist-form" data-target="<?= $checklist_id ?>">
    <? foreach ($checklist_options as $checklist_option): ?>
        <label>
            <input type="checkbox" value="1"/>
            <span><?= $checklist_option['name'] ?></span>
            <a href="#!" class="delete_item" data-target="<?= $checklist_option['id'] ?>">
                <i class="material-icons">clear</i>
            </a>
        </label>
    <? endforeach; ?>
</div>
<?php //$addItem = ActiveForm::begin([
//    'options' => [
//        'class' => 'checklist-setup-form'
//    ]
//]);?>
<!--    --><?//=$addItem->field($checklist_items, $checklist_items->name) ?>
<!--    --><?//=$addItem->field($checklist_items, $checklist_items->checklist_id)->hiddenInput()?>
<!---->
<!--    <button type="submit" class="btn">Добавить пункт</button>-->
<!---->
<?php //ActiveForm::end() ?>
<!--<form action="/checklist/default/add-checklist-item" type="post" class="checklist-setup-form">-->
<!--    <input class="item-text" type="text" name="item_name" placeholder="введите название">-->
<!--    <input type="hidden" name="checklist_id" value="--><?//= $checklist_id ?><!--">-->
<!--    <button type="submit" class="btn">Добавить пункт</button>-->
<!--</form>-->
