<?php
/* @var $posts \frontend\modules\insta\models\Post */
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
            </div>
        </div>
    <? endforeach; ?>

<?php $this->registerJsFile('@web/js/insta.js', ['depends' => 'yii\web\JqueryAsset']);?>
