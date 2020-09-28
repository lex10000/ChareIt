<?php
/** @var $friends array список друзей */
/** @var $incomingRequests array список исходящих заявок */
/** @var $outgoingRequests array список входящих заявок */
/** @var $this \yii\web\View список входящих заявок */

$this->title = 'Друзья';

?>
<h3>Мои заявки в друзья</h3>
<?= $this->render('friendsList', [
     'friends' => $outgoingRequests
]) ?>
<h3>Запросы в друзья</h3>
<?= $this->render('friendsList', [
    'friends' => $incomingRequests
]) ?>
<h3>Мои друзья</h3>
<?= $this->render('friendsList', [
    'friends' => $friends
]) ?>