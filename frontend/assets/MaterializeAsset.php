<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MaterializeAsset extends AssetBundle
{
    public $sourcePath = '@npm/materialize-css/dist';
    public $css = [
        'css/materialize.css'
    ];
    public $js = [
        'js/materialize.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
