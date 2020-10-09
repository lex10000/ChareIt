<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'user' => [
            'class' => 'frontend\modules\user\Module',
        ],
        'chareit' => [
            'class' => 'frontend\modules\chareit\Module',
        ],
    ],
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,   // не опубликовывать комплект
                    'js' => [
                        YII_ENV_DEV ? '//ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.js'
                            : '//ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js',
                    ]
                ],
            ],
            'appendTimestamp' => true,
            'linkAssets' => true,
        ],
        'storage' => [
          'class' => 'frontend\components\Storage',
        ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'frontend\modules\user\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'profile/<user_id:\d+>' => '/chareit/default/profile',
                'get-feed' => '/chareit/default/get-feed',
                'search-friends' => '/chareit/friends/search-friends',
                'settings' => "/chareit/default/settings",
                "get-top" => "/chareit/default/get-top",
                'get-friends' => '/chareit/friends/get-friends',
                'about' => 'site/about',
            ],
        ],
    ],
    'params' => $params,
];

