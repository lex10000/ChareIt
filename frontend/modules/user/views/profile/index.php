<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Мой профиль';
?>

<div class="main-section">
    <a href="<?= Url::to('/insta/default/create')?>" class="section-type purple">Опубликовать фото</a>
    <a href="<?= Url::to('/checklist/default')?>" class="section-type yellow">Чек-листы</a>
    <a href="<?= Url::to('/insta/default/get-feed')?>" class="section-type yellow">Лента</a>
    <?php ActiveForm::begin([
        'action' => '/user/default/logout'
    ]) ?>
    <button class="section-type red"  type="submit" name="action">
        <i class="material-icons" style="margin-right: 10px">exit_to_app</i> Выйти из системы
    </button>
    <?php ActiveForm::end() ?>
</div>
