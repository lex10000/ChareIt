<?php
/* @var $checklist_options array */

/* @var $checklist_id integer */

use yii\widgets\ActiveForm;

?>

<div class="checklist-form" data-target="<?= $checklist_id ?>">
    <div class="checklist_items" data-target="<?= $checklist_id ?>">
        <? if ($checklist_options): ?>
            <? foreach ($checklist_options as $checklist_option): ?>
                <p>
                    <label>
                        <input type="checkbox" value="1"/>
                        <span><?= $checklist_option['name'] ?></span>
                        <a href="#!" class="delete_item" data-target="<?= $checklist_option['id'] ?>">
                            <i class="material-icons">clear</i>
                        </a>
                    </label>
                </p>
            <? endforeach; ?>
        <? else: ?>
            <div class="empty-checklist">Пустой чек-лист</div>
        <? endif; ?>
    </div>

    <form action="#" class="add-checklist-item">
        <input class="item-text" type="text" name="item_name" autofocus placeholder="введите название">
        <input type="submit" class="btn" value="Добавить пункт">
    </form>
</div>

