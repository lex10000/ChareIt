<?php
/** @var $friends array */
$this->title = 'Друзья';

use frontend\modules\chareit\models\Friends;
use frontend\modules\user\models\User;
use yii\helpers\Html;

?>
<div class="friends-list">
    <? foreach ($friends as $friend): ?>
        <? if ($friend['id'] != Yii::$app->user->getId()): ?>
            <div class="card horizontal profile-card friend-card" data-target="<?= $friend['id'] ?>">
                <div class="card-image friend-card__image">
                    <img src="<?= User::getAvatar($friend['picture']) ?>" alt="Здесь была аватарка">
                </div>
                <div class="card-stacked friend-card__content">
                    <div class="card-content">
                        <a href="/profile/<?= $friend['id'] ?>">
                            <p class="title"><?= $friend['username'] ?></p>
                        </a>
                        <div class="friend-card__about"><?= Html::encode($friend['about']) ?></div>
                    </div>
                    <div class="card-action friend-card__links">
                        <? if (Friends::isUserIn($friend['id'], Friends::FRIENDS)): ?>
                            <a href="#!" class="subscribe">Убрать из друзей</a>
                        <? elseif (Friends::isUserIn($friend['id'], Friends::OUTGOING_REQUESTS)): ?>
                            <a href="#!" class="subscribe">Отменить подписку</a>
                        <? elseif (Friends::isUserIn($friend['id'], Friends::INCOMING_REQUESTS)): ?>
                            <a href="#!" class="confirmRequest" data-target="1">Добавить</a>
                            <div></div>
                            <a href="#!" class="confirmRequest" data-target="0">Отклонить</a>
                        <? else: ?>
                            <a href="#!" class="subscribe">Добавить</a>
                        <? endif; ?>
                    </div>
                </div>
            </div>
        <? endif; ?>
    <? endforeach; ?>
</div>

