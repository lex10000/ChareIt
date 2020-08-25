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

