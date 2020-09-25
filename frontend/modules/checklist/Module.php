<?php

namespace frontend\modules\checklist;

/**
 * Checklist module definition class
 */
class Module extends \yii\base\Module
{
    public $layout = 'checklistLayout';

    public $controllerNamespace = 'frontend\modules\checklist\controllers';

    public function init()
    {
        parent::init();
    }
}
