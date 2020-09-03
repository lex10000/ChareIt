<?php
/* @var $posts \frontend\models\Post */
/* @var $this \yii\web\View */

$this->title = 'Лентач';

?>
    <? foreach ($posts as $post): ?>
        <div class="card">
            <div class="card-image">
                <img src="<?= '/uploads/'.$post['filename']?>">
                <span class="card-title"><?= 'Какая то новая фотография, этого пункта еще нет в базе данных' ?></span>
            </div>
            <div class="card-content">
                <p><?= $post['description']?></p>
            </div>
            <div class="card-action">
                <!--                    <a href="#">This is a link</a>-->
            </div>
        </div>
    <? endforeach; ?>

<?php $this->registerJsFile('@web/js/insta.js', ['depends' => 'yii\web\JqueryAsset']);?>
