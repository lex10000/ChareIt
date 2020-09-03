<?php

namespace frontend\modules\checklist;

/**
 * Checklist module definition class
 */
class Module extends \yii\base\Module
{
    public $layout = '@app/views/layouts/checklistLayout';
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'frontend\modules\checklist\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
