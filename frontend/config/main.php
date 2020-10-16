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
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['1.2.3.4', '127.0.0.1', '::1']
        ],
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
                'frontend\assets\ReactAsset' => [
                    'sourcePath' => null,   // не опубликовывать комплект
                    'js' => [
                        !YII_ENV_DEV ? 'https://unpkg.com/react@16/umd/react.production.min.js' : 'https://unpkg.com/react@16/umd/react.development.js',
                        !YII_ENV_DEV ? 'https://unpkg.com/react-dom@16/umd/react-dom.production.min.js': "https://unpkg.com/react-dom@16/umd/react-dom.development.js"
                    ],
                    'jsOptions' => [
                        'crossorigin' => true
                    ]
                ]
            ],
            'appendTimestamp' => true,
            'linkAssets' => true,
        ],
        'storage' => [
          'class' => 'frontend\components\Storage',
        ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
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
                'get-feed/<startPage:\d*>' => '/chareit/default/get-feed',
                'search-friends' => '/chareit/friends/search-friends',
                'settings' => "/chareit/default/settings",
                "get-top" => "/chareit/default/get-top",
                'get-friends' => '/chareit/friends/get-friends',
                'about' => 'site/about',
                'liked-users/<post_id:\d+>' => '/chareit/friends/liked-users'
            ],
        ],

    ],
    'params' => $params,

];


