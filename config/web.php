<?php
require(__DIR__ . '/.runtime.php');

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params_api.php'),
    require(__DIR__ . '/api/appid_token.php'),
    require(__DIR__ . '/api/route_permission.php')
);

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'language'      => 'zh-CN',
    'bootstrap' => ['log'],
    'modules'       => require(__DIR__ . '/modules.php'),
    'defaultRoute'  => "/test/test/test",
    'controllerMap' => require(__DIR__ . '/api/apicontrollermap.php'),
    'components' => [
        'user'          => [
            'identityClass'   => 'app\packages\Auth\IdentityModel',
            'enableAutoLogin' => true,
            'enableSession'   => false,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'X1ZwXDL15UDxSS_OfkkpXXdpjk5k5K6-',
            'parsers'             => [
                'application/json' => 'yii\web\JsonParser',
                'text/json'        => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log'           => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'   => 'app\packages\Log\SysMonologTarget',
                    'levels'  => ['error', 'warning'],
                    'channel' => 'systemLog',
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager'    => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => require(__DIR__ . '/api/route.php'),
        ],
        'monolog'       => require(__DIR__ . '/monolog.php'),
        'i18n'          => require(__DIR__ . '/i18n.php'),
        'rest'         => [
            'class' => 'app\components\RestComponent',
        ],
        'langComponent' => [
            'class' => 'app\packages\Lang\Lang',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
