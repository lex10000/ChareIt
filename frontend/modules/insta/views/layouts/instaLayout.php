<?php

/* @var $this \yii\web\View */

/* @var $content string */

use frontend\modules\user\models\User;
use yii\helpers\Html;
use frontend\assets\InstaAsset;
use yii\widgets\ActiveForm;
use frontend\widgets\HealthWidget\HealthWidget;

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
<!--        --><?//= HealthWidget::widget(['workTime' => 50, 'healthTime' => 10]) ?>
    <div class="insta_app">
        <div class="insta_menu">
            <ul class="sidenav sidenav-fixed">
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
                    <a href="/get-feed"><i class="material-icons">satellite</i>Лента</a>
                </li>
                <li>
                    <div class="divider"></div>
                </li>
                <li>
                    <a href="/get-feed/<?= Yii::$app->user->getId() ?>"><i class="material-icons">local_see</i> Мои
                        посты</a>
                </li>
                <li>
                    <div class="divider"></div>
                </li>
                <li>
                    <a href='/get-friends'><i class="material-icons">supervisor_account</i>Мои друзья
                        <? if ($count = \frontend\modules\insta\models\Friends::getFriendsRequestCount() !== 0): ?>
                            <span class="new badge">
                        <?= $count ?>
                    </span>
                        <? endif; ?>
                    </a>
                </li>
                <li>
                    <div class="divider"></div>
                </li>
                <li>
                    <a href="/get-top"><i class="material-icons">star</i>Топ постов</a>
                </li>
                <li>
                    <div class="divider"></div>
                </li>
                <li>
                    <a href="/settings"><i class="material-icons">settings</i>Настройки</a>
                </li>
                <li>
                    <div class="divider"></div>
                </li>
                <li>
                    <a href="/search-friends"><i class="material-icons">search</i>Поиск друзей</a>
                </li>
                <li>
                    <div class="divider"></div>
                </li>
            </ul>
            <div class="mobile-menu__top">
                    <a href="/get-feed/<?= Yii::$app->user->getId() ?>">
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
            <div class="mobile-menu__bottom">
                <ul>
                    <li>
                        <a href="#modal1" class="get_create_form modal-trigger"><i class="material-icons">cloud</i></a>
                    </li>
                    <li>
                        <a href="/get-feed"><i class="material-icons">satellite</i></a>
                    </li>
                    <li>
                        <a href="/search-friends"><i class="material-icons">search</i></a>
                    </li>
                    <li>
                        <a href="/settings"><i class="material-icons">settings</i></a>
                    </li>
                    <li>
                        <a href="/get-top"><i class="material-icons">star</i></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="insta_main_page">
            <div id="user_info">
                <?= $this->blocks['user_info'] ?? null ?>
            </div>
            <div>
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