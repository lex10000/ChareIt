<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReactAppAsset extends AssetBundle
{
    public $css = [
    ];
    public $js = [
        'js/reactApp.js'
    ];
    public $depends = [
        'frontend\assets\ReactAsset'
    ];
    public $jsOptions = [
      'type' => 'module'
    ];
}
