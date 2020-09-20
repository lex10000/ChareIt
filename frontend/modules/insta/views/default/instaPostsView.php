<?php
/* @var $posts array массив с постами */
/* @var $this \yii\web\View */
/* @var $user User информация для блока о пользователе */
/* @var $isSubscriber bool является ли пользователь другом */
/* @var $renderUserInfo bool выводить инфу о юзере, или нет (например, для ajax запросов)*/

$this->title = 'Лентач';
use frontend\modules\insta\models\Post;
use yii\helpers\Html;
use frontend\modules\user\models\User;
?>

<?php /** Блок с информацией о текущем пользователе. Не выводится, если ajax запрос, или если запрашивается общая лента*/?>
<? if(isset($renderUserInfo)) :?>
    <?php $this->beginBlock('user_info'); ?>
    <div class="user_card" data-target="<?=$user->id?>">
        <img src="<?= User::getAvatar($user->picture)?>" alt="avatar" class="circle">
        <a href="/insta/get-feed/<?=$user->id?>"><span class="title"><?=$user->username?></span></a>
        <? if($user->id !== Yii::$app->user->id): ?>
            <? if ($isSubscriber): ?>
                <a href="#!" class="subscribe">Отписаться</a>
            <? else: ?>
                <a href="#!" class="subscribe">Подписаться</a>
            <? endif; ?>
        <? endif; ?>
    </div>
    <?php $this->endBlock(); ?>
<? endif; ?>
<?php /** конец блока*/?>

<? foreach ($posts as $post): ?>
    <div class="card" data-target="<?= $post['id'] ?>">
        <div class="card-image">
            <img class="materialboxed" src="<?= '/uploads/thumbnails/' . $post['filename'] ?>">
        </div>
        <div class="card-content">
            <p><a href="/insta/get-feed/<?= $post['user_id'] ?>"><?= $post['user_id'] ?></a> </p>
            <p><?= Html::encode($post['description']) ?></p>
            <p class="created_at"><?= $post['created_at'] ?></p>
        </div>
        <div class="card-action" >
            <a href="#!" class="post_like_button" data-target="<?= $post['id'] ?>">
                <i class="material-icons">
                    <? if (Post::isChangedByUser(Yii::$app->user->getId(), $post['id'], 'likes')): ?>
                        favorite
                    <? else : ?>
                        favorite_border
                    <? endif; ?>
                </i>
            </a>
            <a href="#!" class="post_dislike_button">
                <i class="material-icons">
                    <? if (Post::isChangedByUser(Yii::$app->user->getId(), $post['id'], 'dislikes')): ?>
                        thumb_up
                    <? else : ?>
                        thumb_down
                    <? endif; ?>
                </i>
            </a>
            <span class="count_likes"><?= Post::countLikes($post['id']) ?> лайков</span>
            <? if ($post['user_id'] == Yii::$app->user->getId()): ?>
                <a href="#!" class="post_delete_button" data-target="<?= $post['id'] ?>"><i
                            class="material-icons">clear</i></a>
            <? endif; ?>
            <a href="<?= '/uploads/' . $post['filename'] ?>" download><i class="material-icons" >file_download</i></a>
        </div>
    </div>
<? endforeach; ?>

