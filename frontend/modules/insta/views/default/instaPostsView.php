<?php
/* @var $posts array массив с постами */
/* @var $this \yii\web\View */
/* @var $user User информация для блока о пользователе */
/* @var $isRenderUserInfo bool выводить инфу о юзере, или нет (например, для ajax запросов) */

$this->title = 'Лентач';

use frontend\modules\insta\models\Post;
use yii\helpers\Html;
use frontend\modules\user\models\User;
?>

<?php /** Блок с информацией о текущем пользователе. Не выводится, если ajax запрос, или если запрашивается общая лента*/ ?>
<? if (isset($isRenderUserInfo)) : ?>
    <?php $this->beginBlock('user_info'); ?>
        <?= $this->render('/friends/friendsList', [
          'friends' => $user
    ]); ?>
    <?php $this->endBlock(); ?>
<? endif; ?>
<?php /** конец блока*/ ?>

<div class="insta_posts">
    <? foreach ($posts as $post): ?>
        <div class="card" data-target="<?= $post['id'] ?>">
            <div class="card-content user_card__header">
                <div class="user_card__header">
                    <div>
                        <a href="/get-feed/<?= $post['user_id'] ?>">
                            <img src="<?= User::getAvatar($post['picture']) ?>" class="circle" alt="" />
                        </a>
                    </div>
                    <div>
                        <a class="user_card__header username" href="/get-feed/<?= $post['user_id'] ?>"><?= $post['username'] ?></a>
                        <p class="created_at"><?= Yii::$app->formatter->asDate($post['created_at']) ?></p>
                    </div>
                </div>
                <div>
                    <? if ($post['user_id'] == Yii::$app->user->getId()): ?>
                        <a href="#!" class="post_delete_button" data-target="<?= $post['id'] ?>"><i
                                    class="material-icons">clear</i></a>
                    <? endif; ?>
                </div>
            </div>
            <div class="divider"></div>
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
                    <a href="<?= '/uploads/' . $post['filename'] ?>" download><i class="material-icons">file_download</i></a>
                </div>
            </div>
        </div>
    <? endforeach; ?>
</div>


