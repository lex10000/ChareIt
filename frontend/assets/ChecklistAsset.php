<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ChecklistAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/checklist.css'
    ];
    public $js = [
        'js/app.js'
    ];
    public $depends = [
    ];
    public $jsOptions = [
        'type' => 'module',
    ];

}