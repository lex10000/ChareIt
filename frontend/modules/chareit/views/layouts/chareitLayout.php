<?php

/* @var $this \yii\web\View */

/* @var $content string */

use frontend\modules\user\models\User;
use yii\helpers\Html;
use frontend\assets\ChareitAsset;
use yii\widgets\ActiveForm;
use frontend\widgets\HealthWidget\HealthWidget;
use frontend\modules\chareit\models\Friends;

ChareitAsset::register($this);
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
    <!--    --><? //= HealthWidget::widget(['workTime' => 30, 'healthTime' => 1]) ?>
    <div class="app">
        <div class="menu">
            <div class="menu-nav">
                <div class="menu-nav__user">
                    <a class="circle" href="/get-feed">
                        <img  src="<?= User::getAvatar(Yii::$app->user->identity->picture) ?>">
                    </a>
                    <div>
                        <?= Yii::$app->user->identity->username ?>
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
                                class="badge"><?= $count ?></span><? endif; ?></a>
                    <a class="menu-nav__item" href="/get-top"><i class="material-icons">star</i>Топ постов</a>
                    <a class="menu-nav__item" href="/settings"><i class="material-icons">settings</i>Настройки</a>
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
                <a href="/profile/<?= Yii::$app->user->getId() ?>">
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
                        <a href="/get-friends"><i class="material-icons">supervisor_account</i>Друзья
                            <? if ($count = Friends::getFriendsRequestCount() !== 0): ?><span
                                    class="badge"><?= '.' ?></span><? endif; ?>
                        </a>

                    </li>
                    <li>
                        <a href="/get-top"><i class="material-icons">star</i>Топ</a>
                    </li>
                    <li>
                        <a href="/settings"><i class="material-icons">settings</i>Настройки</a>
                    </li>

                </ul>
            </div>
        </div>
        <div class="main-page">
            <? if (Yii::$app->session->hasFlash('server-error')): ?>
                <div class="danger_message">
                    <?= Yii::$app->session->getFlash('server-error') ?>
                </div>
            <? endif; ?>
            <?= $content ?>
            <? if (Yii::$app->session->hasFlash('access-denied')): ?>
                <div class="warning_message">
                    <?= Yii::$app->session->getFlash('access-denied') ?>
                </div>
            <? endif; ?>
        </div>
        <?= $this->render('/default/forms/create') ?>
    </div>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>