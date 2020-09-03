<?php

/* @var $this yii\web\View */
/* @var $model \frontend\modules\user\models\SignupForm */

use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin() ?>
        <?= $form->field($model, 'username', ['options' => ['class' => 'input-field'], 'enableAjaxValidation' => true])->textInput()->label('логин')?>
        <?= $form->field($model, 'password', ['options' => ['class' => 'input-field']])->passwordInput()->label('пароль') ?>
        <?= $form->field($model, 'password_repeat', ['options' => ['class' => 'input-field']])->passwordInput()->label('Повторите пароль') ?>

        <button class="btn waves-effect waves-light purple" type="submit">Зарегистрироваться</button>
<?php ActiveForm::end() ?>