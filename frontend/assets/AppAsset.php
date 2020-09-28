<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/WelcomePage.css',
    ];
    public $js = [
        'js/script.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\MaterializeAsset'
    ];
}
