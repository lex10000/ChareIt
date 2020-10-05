<?php
/* @var $user User информация  о пользователе */
/* @var $posts array массив с постами */
/* @var $this \yii\web\View */

use frontend\modules\user\models\User;
use frontend\modules\insta\models\Friends;
use yii\helpers\Html;
?>
<div class="profile" data-target="<?= $user['id'] ?>">
    <a href="/profile/<?= $user['id'] ?>">
        <img class="circle" src="<?= User::getAvatar($user['picture']) ?>" alt="Здесь была аватарка">
        <p class="title"><?= Html::encode($user['username']) ?></p>
    </a>
    <? if($user['id'] != Yii::$app->user->getId()): ?>
        <div>
            <div class="profile__about"><?= Html::encode($user['about']) ?></div>
            <div class="profile__links">
                <? if (Friends::isUserIn($user['id'], Friends::FRIENDS)): ?>
                    <a href="#!" class="subscribe btn purple">Убрать из друзей</a>
                <? elseif (Friends::isUserIn($user['id'], Friends::OUTGOING_REQUESTS)): ?>
                    <a href="#!" class="subscribe btn purple">Отменить подписку</a>
                <? elseif (Friends::isUserIn($user['id'], Friends::INCOMING_REQUESTS)): ?>
                    <a href="#!" class="confirmRequest btn purple" data-target="1">Добавить в друзья</a>
                    <div></div>
                    <a href="#!" class="confirmRequest btn purple" data-target="0">Отклонить приглашение</a>
                <? else: ?>
                    <a href="#!" class="subscribe btn purple">Добавить в друзья</a>
                <? endif; ?>
            </div>
        </div>
    <? else: ?>
        <div class="">
            <div class="profile__about"><?= Html::encode($user['about']) ?></div>
        </div>
    <? endif; ?>

</div>
<? if($posts): ?>
<?= $this->render('postsView', [
    'posts' => $posts
]);?>
<? else: ?>
    <div class="posts-empty">
        Пока что постов нет...
    </div>
<? endif; ?>
