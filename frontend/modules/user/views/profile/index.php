<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Мой профиль';
?>

<div class="main-section">
    <div class="section-type purple">Заметки</div>
    <a href="<?= Url::to('/checklist/default')?>" class="section-type yellow">Чек-листы</a>
    <a href="<?= Url::to('/post/default/create')?>" class="section-type yellow">Опубликовать фото</a>
    <?php ActiveForm::begin([
        'action' => '/user/default/logout'
    ]) ?>
    <button class="section-type red"  type="submit" name="action">
        <i class="material-icons" style="margin-right: 10px">exit_to_app</i> Выйти из системы
    </button>
    <?php ActiveForm::end() ?>
</div>
