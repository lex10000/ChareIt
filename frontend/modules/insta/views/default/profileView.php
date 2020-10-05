<?php
/* @var $user User информация  о пользователе */
/* @var $posts array массив с постами */

/* @var $this \yii\web\View */

use frontend\modules\user\models\User;
use frontend\modules\insta\models\Friends;
use yii\helpers\Html;

?>
<div class="profile profile-card" data-target="<?= $user['id'] ?>">
    <? if ($user['id'] != Yii::$app->user->getId()): ?>
        <div class="card horizontal profile-card friend-card" data-target="<?= $user['id'] ?>">
            <div class="card-image friend-card__image">
                <img src="<?= User::getAvatar($user['picture']) ?>" alt="Здесь была аватарка">
            </div>
            <div class="card-stacked friend-card__content">
                <div class="card-content">
                    <a href="/profile/<?= $user['id'] ?>">
                        <p class="title"><?= $user['username'] ?></p>
                    </a>
                    <div class="friend-card__about"><?= Html::encode($user['about']) ?></div>
                </div>
                <div class="card-action friend-card__links">
                    <? if (Friends::isUserIn($user['id'], Friends::FRIENDS)): ?>
                        <a href="#!" class="subscribe">Убрать из друзей</a>
                    <? elseif (Friends::isUserIn($user['id'], Friends::OUTGOING_REQUESTS)): ?>
                        <a href="#!" class="subscribe">Отменить подписку</a>
                    <? elseif (Friends::isUserIn($user['id'], Friends::INCOMING_REQUESTS)): ?>
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
</div>
<? if ($posts): ?>
    <? if ($posts === 'empty'): ?>
        <div class="posts-empty">
            Пока что постов нет...
        </div>
    <? else: ?>
        <?= $this->render('postsView', [
            'posts' => $posts
        ]); ?>
    <? endif; ?>
<? endif; ?>
