<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ChareitAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/chareit.css'
    ];
    public $js = [
        'js/script.js',
        'js/chareit.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\MaterializeAsset'
    ];
}