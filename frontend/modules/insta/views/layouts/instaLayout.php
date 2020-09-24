<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\modules\user\models\User;
use yii\helpers\Html;
use frontend\assets\InstaAsset;
use yii\widgets\ActiveForm;
use frontend\modules\insta\models\forms\PostForm;
use frontend\widgets\HealthWidget\HealthWidget;

$postForm = new PostForm(Yii::$app->user->getId());
InstaAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <?php $this->head() ?>
    </head>
    <?php $this->beginBody() ?>
    <body>
    <!--    --><? //= HealthWidget::widget(['workTime' => 50, 'healthTime' => 1]) ?>
    <div class="insta_app">
        <ul class="sidenav sidenav-fixed insta_menu">
            <li>
                <div class="user_info">
                    <a href="/insta/get-feed">
                        <img class="circle" src="<?= User::getAvatar(Yii::$app->user->identity->picture) ?>">
                    </a>
                    <div><?= Yii::$app->user->identity->username ?></div>
                    <?php ActiveForm::begin([
                        'action' => '/user/default/logout'
                    ]) ?>
                    <button type="submit" class="btn purple">
                        <i class="material-icons">exit_to_app</i>
                    </button>
                    <?php ActiveForm::end() ?>
                </div>
            </li>
            <li>
                <div class="divider"></div>
            </li>
            <li>
                <a href="#modal1" class="get_create_form modal-trigger"><i class="material-icons">cloud</i>Опубликовать
                    фото</a>
            </li>
            <li>
                <div class="divider"></div>
            </li>
            <li>
                <a href="/insta/get-feed"><i class="material-icons">satellite</i>Лента</a>
            </li>
            <li>
                <div class="divider"></div>
            </li>
            <li>
                <a href="/insta/get-feed/<?= Yii::$app->user->getId() ?>"><i class="material-icons">local_see</i> Мои
                    посты</a>
            </li>
            <li>
                <div class="divider"></div>
            </li>
            <li>
                <a href='/insta/friends/get-friends'><i class="material-icons">supervisor_account</i>Мои друзья</a>
            </li>
            <li>
                <div class="divider"></div>
            </li>
            <li>
                <a href="/insta/default/get-top"><i class="material-icons">star</i>Топ постов</a>
            </li>
            <li>
                <div class="divider"></div>
            </li>
            <li>
                <a href="/user/default/settings"><i class="material-icons">settings</i>Настройки</a>
            </li>
            <li>
                <div class="divider"></div>
            </li>
        </ul>
        <div class="insta_main_page">
            <div id="user_info">
                <?= $this->blocks['user_info'] ?? null ?>
            </div>
            <div class="insta_posts">
                <?= $content ?>
            </div>
        </div>
    </div>
    <!-- Modal Structure -->
    <div id="modal1" class="modal">
        <div class="modal-content">
            <h3>Добавьте фотографию</h3>
            <div class="create_post_form">
                <?php $form = ActiveForm::begin([
                    'action' => '/insta/default/create',
                    'options' => [
                        'class' => 'create_post',
                    ]
                ]); ?>
                <?= $form->field($postForm, 'picture')->fileInput() ?>
                <?= $form->field($postForm, 'description')->textInput() ?>
                <input type="submit" class="btn" value="Создать">
                <a href="#!" class="modal-close waves-effect waves-green btn-flat">Закрыть</a>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
        <!-- end Modal Structure -->
        <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>