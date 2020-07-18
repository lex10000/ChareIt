<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\modules\user\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin([
    'action' => '/user/default/signup',
    'options' => ['class' => 'col s12'],
]) ?>
    <div class="row welcome__login-auth">
        <?= $form->field($model, 'username', ['options' => ['class' => 'input-field col s12 ']])->textInput()->label('логин')?>
        <?= $form->field($model, 'password', ['options' => ['class' => 'input-field col s12 ']])->passwordInput()->label('пароль') ?>
        <?= $form->field($model, 'password_repeat', ['options' => ['class' => 'input-field col s12 ']])->passwordInput()->label('Повторите пароль') ?>

        <button class="btn waves-effect waves-light purple" type="submit" name="action">Зарегистрироваться</button>
    </div>
<?php ActiveForm::end() ?>