<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;

\frontend\assets\MaterializeAsset::register($this);

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
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="preloader-wrapper">
    <div class="spinner-layer spinner-red-only">
        <div class="circle-clipper left">
            <div class="circle"></div>
        </div><div class="gap-patch">
            <div class="circle"></div>
        </div><div class="circle-clipper right">
            <div class="circle"></div>
        </div>
    </div>
</div>
<div class="main-sectionn">
    <div>
        <?= $content ?>
        <ul class="collapsible checklists">

        </ul>
    </div>
    <div class="main-field">

    </div>
<!--    --><?php //\yii\widgets\Pjax::begin([
//        'enablePushState' => false,
//        'timeout' => 5000,
//    ]); ?>
<!--    <div class="checklistForm">-->
<!--        <a href="/checklist/default/create-checklist" class="create-form">Добавить чек-лист</a>-->
<!--    </div>-->
<!--    --><?php //\yii\widgets\Pjax::end(); ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
