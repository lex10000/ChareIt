<?php
/** @var array $users */
/** @var \yii\web\View $this */
?>
    <?= $this->render('friendsList', [
            'friends' => $users
    ])?>
