<?php
/** @var $friends array */
$this->title = 'Друзья';

use frontend\modules\insta\models\Friends;
use frontend\modules\user\models\User;
?>
<div class="friends">
    <? foreach ($friends as $friend): ?>
        <? if ($friend['id'] != Yii::$app->user->getId()): ?>
            <div class="user_card" data-target="<?= $friend['id'] ?>">
                <a href="/get-feed/<?= $friend['id'] ?>">
                    <img src="<?= User::getAvatar($friend['picture']) ?>" alt="Здесь была аватарка" class="circle">
                    <p class="title"><?= $friend['username'] ?></p>
                </a>
                <div><?= $friend['about'] ?></div>
                <div class="user_links">
                    <? if(Friends::isUserIn($friend['id'], Friends::FRIENDS)):?>
                    <a href="#!" class="subscribe btn">Убрать из друзей</a>
                    <? elseif(Friends::isUserIn($friend['id'], Friends::OUTGOING_REQUESTS)):?>
                    <a href="#!" class="subscribe btn">Отменить подписку</a>
                    <? elseif(Friends::isUserIn($friend['id'], Friends::INCOMING_REQUESTS)):?>
                    <a href="#!" class="confirmRequest btn" data-target="1">Добавить в друзья</a>
                    <div></div>
                    <a href="#!" class="confirmRequest btn" data-target="0">Отклонить приглашение</a>
                    <? else: ?>
                    <a href="#!" class="subscribe btn" >Добавить в друзья</a>
                    <? endif; ?>
                </div>
            </div>
        <? endif; ?>
    <? endforeach; ?>
</div>

