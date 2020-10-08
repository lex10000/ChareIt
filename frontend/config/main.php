<?php
//check work of git asdasdasdasdasdas
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
        'insta' => [
            'class' => 'frontend\modules\insta\Module',
        ],
    ],
    'components' => [
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
                'profile/<user_id:\d+>' => '/insta/default/profile',
                'get-feed' => '/insta/default/get-feed',
                'search-friends' => '/insta/friends/search-friends',
                'settings' => "/insta/default/settings",
                "get-top" => "/insta/default/get-top",
                'get-friends' => '/insta/friends/get-friends',
                'about' => 'site/about',
            ],
        ],
    ],
    'params' => $params,
];

