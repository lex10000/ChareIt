<?php

/**
 * AssetBundle для виджета HealthWidget
 * @author alex_ved
 */
namespace frontend\widgets\HealthWidget;

use yii\web\AssetBundle;

class HealthWidgetAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/src';

    public $publishOptions = [
        'forceCopy' => true
    ];

    public $js = [
        'js/HealthWidget.js'
    ];

    public $css = [
        'css/HealthWidget.css'
    ];
}