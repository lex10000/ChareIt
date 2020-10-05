<?php
/** @var $friends array */
$this->title = 'Друзья';

use frontend\modules\insta\models\Friends;
use frontend\modules\user\models\User;
use yii\helpers\Html;
?>
<div class="friends-list">
    <? foreach ($friends as $friend): ?>
        <? if ($friend['id'] != Yii::$app->user->getId()): ?>
            <div class="friend-card" data-target="<?= $friend['id'] ?>">
                <a href="/profile/<?= $friend['id'] ?>">
                    <img src="<?= User::getAvatar($friend['picture']) ?>" alt="Здесь была аватарка" class="circle">
                    <p class="title"><?= $friend['username'] ?></p>
                </a>
                <div>
                    <div class="friend-card__about"><?= Html::encode($friend['about'] ) ?></div>
                    <div class="friend-card__links">
                        <? if(Friends::isUserIn($friend['id'], Friends::FRIENDS)):?>
                            <a href="#!" class="subscribe btn purple">Убрать из друзей</a>
                        <? elseif(Friends::isUserIn($friend['id'], Friends::OUTGOING_REQUESTS)):?>
                            <a href="#!" class="subscribe btn purple">Отменить подписку</a>
                        <? elseif(Friends::isUserIn($friend['id'], Friends::INCOMING_REQUESTS)):?>
                            <a href="#!" class="confirmRequest btn purple" data-target="1">Добавить</a>
                            <div></div>
                            <a href="#!" class="confirmRequest btn purple" data-target="0">Отклонить</a>
                        <? else: ?>
                            <a href="#!" class="subscribe btn purple" >Добавить</a>
                        <? endif; ?>
                    </div>
                </div>
            </div>
        <? endif; ?>
    <? endforeach; ?>
</div>

