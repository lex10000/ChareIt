<?php
/** @var \frontend\modules\insta\models\forms\SearchModel $searchModel */

/** @var array $friends */
/** @var \yii\web\View $this */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>
<div class="friends_search">
    <h3>Поиск друзей</h3>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($searchModel, 'userQuery')
        ->textInput(['placeholder'=>'Введите имя пользователя (минимум 3 символа)']) ?>
    <input type="submit" class="btn" value="Поиск">
    <?php ActiveForm::end(); ?>
</div>

<? if (isset($friends)): ?>
    <?= $this->render('friendsList', [
            'friends' => $friends
    ])?>
<? endif; ?>
