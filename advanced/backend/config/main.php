<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'gridview' =>[
            'class' => '\kartik\grid\Module',
//            'class' => 'yii\i18n\PhpMessageSource',
//            'basePath' => '@kvgrid/messages',
//            'forceTranslation' => true
        ],
        'admin' => [
            'class' => 'mdm\admin\Module',
//            'layout' => 'left-menu',//yii2-admin的导航菜单
        ],
        //'gii' => [
            //'class' => 'yii\gii\Module',
            //'allowedIPs' => ['127.0.0.1', '192.168.0.*','::1'] // 按需调整这里
        //],
//        'debug' => [
//            'class' => 'yii\gii\Module',
//            'allowedIPs' => ['127.0.0.1', '192.168.0.*','::1'] // 按需调整这里
//        ],

    ],
    'aliases' => [
        '@mdm/admin' => '@vendor/mdmsoft/yii2-admin',
        '@unitools' => '@app/unitools',
    ],
    'language' => 'zh-CN',
    'components' => [
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest'],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:d-M-Y',
            'datetimeFormat' => 'php:d-M-Y H:i:s',
            'timeFormat' => 'php:H:i:s',
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'backend\models\User',
            'enableAutoLogin' => true,
//            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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

        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/cache2',
        ],
        'local_cache' => [
            'class' => 'yii\caching\ApcCache',
            'useApcu' => true ,
        ],
        'urlManager' => [
            'enablePrettyUrl' => false,
            'showScriptName' => false,
        ],



    ],

    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            //这里是允许访问的action，不受权限控制
            //controller/action
            'site/*',
            'admin/*',
            'user/*',
            '/*',

        ]
    ],
    'params' => $params,
    //语言
    //时区
    'timeZone' => 'Asia/Chongqing',
];
