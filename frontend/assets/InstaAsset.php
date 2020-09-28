<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class InstaAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/insta.css'
    ];
    public $js = [
        'js/script.js',
        'js/insta.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\MaterializeAsset'
    ];
}