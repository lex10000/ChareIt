<?php
/* @var $user User информация  о пользователе */
/* @var $posts array массив с постами */
/* @var $this \yii\web\View */

use frontend\modules\user\models\User;
use frontend\modules\insta\models\Friends;
use yii\helpers\Html;

?>
<div class="userView" data-target="<?= $user['id'] ?>">
    <a href="/get-feed/<?= $user['id'] ?>">
        <img src="<?= User::getAvatar($user['picture']) ?>" alt="Здесь была аватарка">
        <p class="title"><?= Html::encode($user['username']) ?></p>
    </a>
    <div><?= Html::encode($user['about']) ?></div>
    <div class="user_links">
        <? if (Friends::isUserIn($user['id'], Friends::FRIENDS)): ?>
            <a href="#!" class="subscribe btn">Убрать из друзей</a>
        <? elseif (Friends::isUserIn($user['id'], Friends::OUTGOING_REQUESTS)): ?>
            <a href="#!" class="subscribe btn">Отменить подписку</a>
        <? elseif (Friends::isUserIn($user['id'], Friends::INCOMING_REQUESTS)): ?>
            <a href="#!" class="confirmRequest btn" data-target="1">Добавить в друзья</a>
            <div></div>
            <a href="#!" class="confirmRequest btn" data-target="0">Отклонить приглашение</a>
        <? else: ?>
            <a href="#!" class="subscribe btn">Добавить в друзья</a>
        <? endif; ?>
    </div>
</div>
<? if($posts): ?>
<?= $this->render('postsView', [
    'posts' => $posts
]);?>
<? endif; ?>
