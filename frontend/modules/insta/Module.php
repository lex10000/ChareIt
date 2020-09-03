<?php

namespace frontend\modules\insta;

/**
 * insta module definition class
 */
class Module extends \yii\base\Module
{
    public $layout = '@frontend/views/layouts/instaLayout';
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'frontend\modules\insta\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
