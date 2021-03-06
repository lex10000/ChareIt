<?php
/* @var $this yii\web\View */
/* @var $user_id int id пользователя*/

use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Мой профиль';
?>

<div class="main-section">
    <a href="<?= Url::to("/insta/get-feed/$user_id")?>" class="section-type purple">Мои посты</a>
    <a href="<?= Url::to('/insta/default/get-top')?>" class="section-type yellow">Топ постов</a>
    <a href="<?= Url::to('/insta/get-feed')?>" class="section-type yellow">Лента</a>
    <?php ActiveForm::begin([
        'action' => '/user/default/logout'
    ]) ?>
    <button class="section-type red"  type="submit" name="action">
        <i class="material-icons" style="margin-right: 10px">exit_to_app</i> Выйти из системы
    </button>
    <?php ActiveForm::end() ?>
</div>
