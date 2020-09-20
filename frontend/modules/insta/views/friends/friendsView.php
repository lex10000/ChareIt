<?php
/** @var $friends array */
$this->title = 'Друзья';
use frontend\modules\user\models\User;
?>
    <? foreach ($friends as $friend):?>
    <div class="user_card" data-target="<?=$friend['id']?>" >
        <img src="<?= User::getAvatar($friend['picture'])?>" alt="" class="circle">
        <a href="/insta/get-feed/<?=$friend['id']?>"><span class="title"><?=$friend['username']?></span></a>
        <a href="#!" class="subscribe">Отписаться</a>
    </div>
    <? endforeach; ?>
