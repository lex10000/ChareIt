<?php
/* @var $posts array \frontend\modules\insta\models\Post */
/* @var $this \yii\web\View */

$this->title = 'Лентач';

?>
    <? foreach ($posts as $post): ?>
        <div class="card">
            <div class="card-image">
                <img src="<?= '/uploads/'.$post['filename']?>">
            </div>
            <div class="card-content">
                <p><?= $post['user_id']?></p>
                <p><?= $post['description']?></p>
            </div>
            <div class="card-action">
                <a href="#!" class="post_like_button" data-target="<?= $post['id']?>"><i class="material-icons">favorite_border</i></a>
                <? if($post['user_id'] == Yii::$app->user->getId()): ?>
                    <a href="#!" class="post_delete_button" data-target="<?= $post['id']?>"><i class="material-icons">clear</i></a>
                <? endif; ?>
            </div>
        </div>
    <? endforeach; ?>