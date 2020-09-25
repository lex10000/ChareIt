<?php
/* @var $user User информация для блока о пользователе */
/* @var $isSubscriber bool является ли пользователь другом */

use frontend\modules\user\models\User;

?>
<div class="user_card" data-target="<?=$user->id?>">
    <a href="/insta/get-feed/<?=$user->id?>">
        <img src="<?= User::getAvatar($user->picture)?>" alt="" class="circle">
        <p class="title"><?=$user->username?></p>
    </a>
    <div>
        <? if($user->id !== Yii::$app->user->id): ?>
            <? if ($isSubscriber): ?>
                <a href="#!" class="subscribe">Отписаться</a>
            <? else: ?>
                <a href="#!" class="subscribe">Подписаться</a>
            <? endif; ?>
        <? endif; ?>
    </div>
    <div><?= $user->about?></div>
</div>

