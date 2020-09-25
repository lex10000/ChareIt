<?php
/** @var $friends array */
$this->title = 'Друзья';
use frontend\modules\user\models\User;
?>
<div class="friends">
    <? foreach ($friends as $friend):?>
        <div class="user_card" data-target="<?=$friend['id']?>" >
            <a href="/insta/get-feed/<?=$friend['id']?>">
                <img src="<?= User::getAvatar($friend['picture'])?>" alt="" class="circle">
                <p class="title"><?=$friend['username']?></p>
            </a>
            <a href="#!" class="subscribe">Отписаться</a>
        </div>
    <? endforeach; ?>
</div>

