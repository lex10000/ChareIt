<?php
/* @var $posts array массив с постами */
/* @var $this \yii\web\View */

$this->title = 'Лентач';

use frontend\modules\insta\models\Post;
use yii\helpers\Html;
use frontend\modules\user\models\User;
?>

<? foreach ($posts as $post): ?>
    <div class="card" data-target="<?= $post['id'] ?>">
        <div class="card-header">
            <div class="card-header__userinfo">
                <div class="card-header__avatar">
                    <a href="/get-feed/<?= $post['user_id'] ?>">
                        <img class="circle" src="<?= User::getAvatar($post['picture']) ?>" alt=""/>
                    </a>
                </div>
                <div>
                    <a class="card-header__username"
                       href="/get-feed/<?= $post['user_id'] ?>"><?= $post['username'] ?></a>
                    <div class="card-header__created_at"><?= Yii::$app->formatter->asDate($post['created_at']) ?></div>
                </div>
            </div>
            <div class="card-header__deleteLink">
                <? if ($post['user_id'] == Yii::$app->user->getId()): ?>
                    <a href="#!" class="post_delete_button" data-target="<?= $post['id'] ?>">
                        <i class="material-icons">clear</i>
                    </a>
                <? endif; ?>
            </div>
        </div>
        <div class="card-image">
            <img class="materialboxed" src="<?= '/uploads/thumbnails/' . $post['filename'] ?>">
        </div>
        <div class="card-content">
            <p><?= Html::encode($post['description']) ?></p>
        </div>
        <div class="card-action">
            <div>
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
                <div>
                    <span class="count_likes"><?= Post::countLikes($post['id']) ?> лайков</span>

                </div>
            </div>
            <div>
                <a href="<?= '/uploads/' . $post['filename'] ?>" download><i
                            class="material-icons">file_download</i></a>
            </div>
        </div>
    </div>
<? endforeach; ?>


