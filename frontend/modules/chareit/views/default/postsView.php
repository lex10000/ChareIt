<?php
/* @var $posts array массив с постами */
/* @var $this \yii\web\View */

$this->title = 'Лентач';

use frontend\modules\chareit\models\PostLikes;
use yii\helpers\Html;
use frontend\modules\user\models\User;

?>

<? foreach ($posts

            as $post): ?>
    <div class="card post-card" data-target="<?= $post['id'] ?>">
        <div class="card-header">
            <div class="card-header__userinfo">
                <div class="card-header__avatar">
                    <a href="/profile/<?= $post['user_id'] ?>">
                        <img class="circle" src="<?= User::getAvatar($post['picture']) ?>" alt=""/>
                    </a>
                </div>
                <div>
                    <a class="card-header__username"
                       href="/profile/<?= $post['user_id'] ?>"><?= $post['username'] ?></a>
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
                <a href="" class="post_like_button" data-target="<?= $post['id'] ?>">
                    <i class="material-icons">
                        <? if (PostLikes::isChangedByUser(Yii::$app->user->getId(), $post['id'])): ?>
                            favorite
                        <? else : ?>
                            favorite_border
                        <? endif; ?>
                    </i>
                    <span class="likes-counter"><?= PostLikes::countLikes($post['id']) ?> </span>
                </a>
                <div class="liked-users">
                    <div class="liked-users__container">
                        <div class="liked-users__header"></div>
                        <div class="liked-users__close"><i class="material-icons">close</i></div>
                        <div class="liked-users__cards"></div>
                    </div>
                </div>
            </div>
            <div>
                <a href="<?= '/uploads/' . $post['filename'] ?>" download>
                    <i class="material-icons">file_download</i>
                </a>
            </div>
        </div>
    </div>
<? endforeach; ?>



