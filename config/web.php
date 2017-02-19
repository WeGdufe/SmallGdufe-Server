<?php

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/url_config.php')
);
// url_config
// $params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [

        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'HMtb3xxsL3b1ZloN55aXoXEa_QOKHFRk',
            // 'parsers' => [
            //     'application/json' => 'yii\web\JsonParser',
            // ]
            'enableCsrfValidation' => false,
            //关闭CSRF防范，不然post请求会被404
        ],
        // 'response' => [
        //     'format' => yii\web\Response::FORMAT_JSON,
        //     'charset' => 'UTF-8',
        //     // ...
        // ]
        //http://stackoverflow.com/questions/28924672/how-to-convert-an-array-to-json-in-yii2

        'cache' => [
            'class' => 'yii\redis\Cache',
            'keyPrefix' => 'G',
        ],

        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
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
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => YII_DEBUG ? 1 : 1000,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => ['_REQUEST'],
                    'exportInterval' => YII_DEBUG ? 1 : 1000,
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),

        // 'urlManager' => [
        //     'enablePrettyUrl' => true,
        //     'enableStrictParsing' => true,
        //     'showScriptName' => false,
        //     'rules' => [
        //         ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
        //     ],
        // ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ],
    ],
    'params' => $params,
    'modules'=> require(__DIR__.'/modules.php'),
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
