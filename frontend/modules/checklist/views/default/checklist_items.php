<?php
/* @var $checklist_options array */

/* @var $checklist_id integer */

use yii\widgets\ActiveForm;

?>

<div class="checklist-form" data-target="<?= $checklist_id ?>">
    <div class="checklist_items" data-target="<?= $checklist_id ?>">
        <? if ($checklist_options): ?>
            <? foreach ($checklist_options as $checklist_option): ?>
            <div class="checklist-item">
                <label style="display: block">
                    <input type="checkbox" value="1"/>
                    <span><?= $checklist_option['name'] ?></span>
                </label>
                <div>
                    <a href="#!" class="delete_item" data-target="<?= $checklist_option['id'] ?>">
                        <i class="material-icons">clear</i>
                    </a>
                </div>
            </div>
            <? endforeach; ?>
        <? endif; ?>
    </div>
    <div class="empty-checklist empty-checklist-active">Пустой чек-лист</div>

    <form action="#" class="add-checklist-item">
        <input class="item-text" type="text" name="item_name" autofocus placeholder="введите название">
        <input type="submit" class="btn" value="Добавить пункт">
    </form>
</div>

