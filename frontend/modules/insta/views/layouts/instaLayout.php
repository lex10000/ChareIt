<?php

/* @var $this \yii\web\View */

/* @var $content string */

use frontend\modules\user\models\User;
use yii\helpers\Html;
use frontend\assets\InstaAsset;
use yii\widgets\ActiveForm;
use frontend\widgets\HealthWidget\HealthWidget;
use frontend\modules\insta\models\Friends;

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
    <!--        --><? //= HealthWidget::widget(['workTime' => 50, 'healthTime' => 10]) ?>

    <div class="app">
        <div class="menu">
            <div class="menu-nav">
                <div  class="menu-nav__user">
                    <a href="/get-feed">
                        <img class="circle" src="<?= User::getAvatar(Yii::$app->user->identity->picture) ?>">
                    </a>
                    <div>
                        <?= Yii::$app->user->identity->username?>
                    </div>
                </div>
                <div class="menu-nav__items">
                        <a href="#modal1" class="menu-nav__item get_create_form modal-trigger"><i
                                    class="material-icons">cloud</i>Опубликовать фото</a>
                        <a class="menu-nav__item" href="/get-feed"><i class="material-icons">satellite</i>Лента</a>
                        <a class="menu-nav__item" href="/profile/<?= Yii::$app->user->getId() ?>"><i
                                    class="material-icons">local_see</i>Мои
                            посты</a>
                        <a class="menu-nav__item" href='/get-friends'><i class="material-icons">supervisor_account</i>
                            Друзья
                            <? if ($count = Friends::getFriendsRequestCount() !== 0): ?><span
                                    class="new badge"><?= $count ?></span><? endif; ?></a>
                        <a class="menu-nav__item" href="/get-top"><i class="material-icons">star</i>Топ постов</a>
                        <a class="menu-nav__item" href="/settings"><i class="material-icons">settings</i>Настройки</a>
                        <a class="menu-nav__item" href="/search-friends"><i class="material-icons">search</i>Поиск
                            друзей</a>
                        <a href="" class="menu-nav__item">
                            <?php ActiveForm::begin([
                                'action' => '/user/default/logout'
                            ]) ?>
                            <button type="submit "><i class="material-icons">exit_to_app</i>Выйти</button>
                            <?php ActiveForm::end() ?>
                        </a>
                </div>
            </div>
            <div class="mobile-menu__top">
                <a href="/get-feed/<?= Yii::$app->user->getId() ?>">
                    <img class="circle" src="<?= User::getAvatar(Yii::$app->user->identity->picture) ?>">
                </a>
                <div><?= Yii::$app->user->identity->username ?></div>
                <a href="" class="menu-nav__item">
                    <?php ActiveForm::begin([
                        'action' => '/user/default/logout'
                    ]) ?>
                    <button type="submit "><i class="material-icons">exit_to_app</i></button>
                    <?php ActiveForm::end() ?>
                </a>
            </div>
            <div class="mobile-menu__bottom">
                <ul>
                    <li>
                        <a href="#modal1" class="get_create_form modal-trigger"><i class="material-icons">cloud</i>
                        Запостить</a>
                    </li>
                    <li>
                        <a href="/get-feed"><i class="material-icons">satellite</i>Лента</a>
                    </li>
                    <li>
                        <a href="/search-friends"><i class="material-icons">search</i>Поиск друзей</a>
                    </li>
                    <li>
                        <a href="/settings"><i class="material-icons">settings</i>Настройки</a>
                    </li>
                    <li>
                        <a href="/get-top"><i class="material-icons">star</i>Топ</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="insta_main_page">
<!--            <div id="userView">-->
<!--                --><?//= $this->blocks['user_info'] ?? null ?>
<!--            </div>-->
            <div class="post-cards">
                <? if(Yii::$app->session->hasFlash('server-error')): ?>
                    <div class="danger_message">
                        <?= Yii::$app->session->getFlash('server-error') ?>
                    </div>
                <? endif; ?>
                <? if(Yii::$app->session->hasFlash('access-denied')): ?>
                    <div class="warning_message">
                        <?= Yii::$app->session->getFlash('access-denied') ?>
                    </div>
                <? endif; ?>
                <?= $content ?>
            </div>
        </div>
    </div>
    <!-- Modal Structure -->
    <div id="modal1" class="modal">
        <div class="modal-content">
            <?= $this->render('/default/forms/create', []) ?>
        </div>
    </div>
    <!-- end Modal Structure -->
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>