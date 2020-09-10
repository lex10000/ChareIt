<?php
/* @var $posts array \frontend\modules\insta\models\Post */
/* @var $this \yii\web\View */

$this->title = 'Лентач';

use frontend\modules\insta\models\Post;

?>
<? foreach ($posts as $post): ?>
    <div class="card" data-target="<?= $post['id'] ?>">
        <div class="card-image">
            <img src="<?= '/uploads/' . $post['filename'] ?>">
        </div>
        <div class="card-content">
            <p><?= $post['user_id'] ?></p>
            <p><?= $post['description'] ?></p>
            <p class="created_at"><?= $post['created_at'] ?></p>
        </div>
        <div class="card-action" >
            <a href="#!" class="post_like_button" data-target="<?= $post['id'] ?>">
                <i class="material-icons">
                    <? if (Post::isLikedByUser(Yii::$app->user->getId(), $post['id'])): ?>
                        favorite
                    <? else : ?>
                        favorite_border
                    <? endif; ?>
                </i>
            </a>
            <a href="#!" class="post_dislike_button">
                <i class="material-icons">
                    <? if (Post::isDisLikedByUser(Yii::$app->user->getId(), $post['id'])): ?>
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
        </div>
    </div>
<? endforeach; ?>