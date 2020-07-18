<?php
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $users frontend\models\User */

$this->title = 'My Yii Application';
?>

<? foreach ($users as $user): ?>
    <a href="<?= Url::to(['/user/profile/view', 'id' => $user->id])?>"><?= $user->nickname?></a>
    <hr>
<? endforeach; ?>

