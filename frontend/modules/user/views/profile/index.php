<?php
/* @var $this yii\web\View */
/* @var $user frontend\models\User */
/* @var $currentUser frontend\models\User */
/* @var $modelPicture frontend\modules\user\models\forms\PictureForm */

/* @var $posts frontend\models\Post */


use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use dosamigos\fileupload\FileUpload;

$this->title = 'Мой профиль';
?>


<div class="main-section">
    <div class="section-type purple">Заметки</div>
    <a href="<?= Url::to('/checklist/default')?>" class="section-type yellow">Чек-листы</a>
    <div class="section-type pink">Опубликовать фото</div>
    <?php ActiveForm::begin([
        'action' => '/user/default/logout'
    ]) ?>
    <button class="section-type red"  type="submit" name="action">
        <i class="material-icons" style="margin-right: 10px">exit_to_app</i> Выйти из системы
    </button>
    <?php ActiveForm::end() ?>
</div>
