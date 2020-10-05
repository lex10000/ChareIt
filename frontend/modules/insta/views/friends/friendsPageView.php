<?php
/** @var $friends array список друзей */
/** @var $incomingRequests array список исходящих заявок */
/** @var $outgoingRequests array список входящих заявок */
/** @var $searchModel \frontend\modules\insta\models\forms\SearchModel список входящих заявок */
/** @var $this \yii\web\View список входящих заявок */

$this->title = 'Друзья';

use yii\widgets\ActiveForm;

?>
<div class="friends-search">
    <div>Поиск</div>
    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'options' => [
            'class' => 'friends-search__form'
        ]
    ]); ?>
    <?= $form
        ->field($searchModel, 'userQuery', ['options' => ['class' => 'friends-search__field']])
        ->textInput(['placeholder' => 'Введите имя пользователя (минимум 3 символа)'])->label('') ?>
    <?php ActiveForm::end(); ?>
    <div class="friends-search__result"></div>
</div>
<div class="friends">
    <ul id="friends-tabs" class="friends-tabs">
        <li class="tab"><a class="active" href="#friends">Мои друзья</a></li>
        <li class="tab"><a href="#outgoingRequests">Исходящие заявки</a></li>
        <li class="tab"><a href="#incomingRequests">Входящие заявки</a></li>
    </ul>
    <div id="friends"> <?= $this->render('friendsList', [
            'friends' => $friends
        ]) ?></div>
    <div id="outgoingRequests"><?= $this->render('friendsList', [
            'friends' => $outgoingRequests
        ]) ?></div>
    <div id="incomingRequests"><?= $this->render('friendsList', [
            'friends' => $incomingRequests
        ]) ?></div>
</div>

