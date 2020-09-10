<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\widgets\ActiveForm;
use frontend\modules\insta\models\forms\PostForm;

$postForm = new PostForm(Yii::$app->user->getId());


AppAsset::register($this);
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
        <?php $this->registerJsFile('@web/js/insta.js', ['depends' => 'yii\web\JqueryAsset']);?>
        <?php $this->head() ?>
    </head>
    <?php $this->beginBody() ?>
    <body>
    <div class="insta_app">
        <ul class="sidenav sidenav-fixed">
            <li>
                <div class="user-view">
                    
                    <a href="#user"><img class="circle" src="/profile_avatars/6LtljfdC9qw.jpg">Алексей</a>

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
                <a href="#!" class="get_create_form"><i class="material-icons">cloud</i>Опубликовать фото</a>
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
                    <?php ActiveForm::end(); ?>
                </div>
            </li>
            <li>
                <div class="divider"></div>
            </li>

            <!--            <li><a class="subheader">Subheader</a></li>-->
            <!--            <li><a class="waves-effect" href="#!">Third Link With Waves</a></li>-->
        </ul>
        <div class="insta_posts">
            <?= $content ?>
        </div>
    </div>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>