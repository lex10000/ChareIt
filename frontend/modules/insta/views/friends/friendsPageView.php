<?php
/** @var $friends array список друзей */
/** @var $incomingRequests array список исходящих заявок */
/** @var $outgoingRequests array список входящих заявок */
/** @var $this \yii\web\View список входящих заявок */

$this->title = 'Друзья';

?>
<div class="friends">
    <div>
        <div>Мои заявки</div>
        <?= $this->render('friendsList', [
            'friends' => $outgoingRequests
        ]) ?>
    </div>
    <div>
        <div>Запросы в друзья</div>
        <?= $this->render('friendsList', [
            'friends' => $incomingRequests
        ]) ?>
    </div>
    <div>
        <div>Мои друзья</div>
        <?= $this->render('friendsList', [
            'friends' => $friends
        ]) ?>
    </div>
</div>
