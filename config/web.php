<?php

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/url_config.php'),
    require(__DIR__ . '/app_update.php'),
    require(__DIR__ . '/app_tips.php')

);
$update_info =
    require(__DIR__ . '/app_update.php')
;

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
                    'logFile' => '@app/runtime/logs/error.log',
                    'exportInterval' => YII_DEBUG ? 1 : 1000,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['request'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/request.log',
                    'maxFileSize' => 1024 * 20,
                    'maxLogFiles' => 50,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logVars' => [],
                    'categories' => ['response'],
                    'logFile' => '@app/runtime/logs/response.log',
                    'maxFileSize' => 1024 * 20,
                    'maxLogFiles' => 50,
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        //url美化，可从
        //http://localhost:82/index.php?r=work/check-app-update
        //变到
        //http://localhost:82/work/check-app-update
        //暂时放弃，客户端的默认参数不好修改..服务器端的捕获又需要一个个写
        // 'urlManager' => [
        //     'enablePrettyUrl' => true,
        //     'showScriptName' => false,
        //     'enableStrictParsing' => false,
        //     'rules' => [
        //         '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
        //         // '<controller:\w+>/<action:\w+>/<sno:\w+>/<pwd:\w+>'=>'<controller>/<action>',
        //         // '<controller:\w+>/<action:\w+>/<sno:\w+>'=>'<controller>/<action>',
        //         // '<controller:\w+>/<action:\w+>/sno/\d+/content/.+/contact/.+'=>'<controller>/<action>',
        //         '<controller:\w+>/<action:\w+>/sno/<sno:\d+>/content/<content:.+>/contact/<contact:.+>'=>'<controller>/<action>',
        //         '<controller:\w+>/<action:\w+>/zkzh/<zkzh:\d+>/xm/<xm:.+>'=>'<controller>/<action>',
        //         '<controller:\w+>/<action:\w+>/zkzh/<zkzh:\d+>/xm/<xm:.+>'=>'<controller>/<action>',
        //         '<controller:\w+>/<action:\w+>/zkzh/<zkzh:\d+>/xm/<xm:.+>'=>'<controller>/<action>',
        //         // '<controller:\w+>/<action:\w+>/<sno:\d+>/<content:.+>/<contact:.+>'=>'<controller>/<action>',
        //     ]
        // ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',  //不要写localhost，会因DNS解析访问变慢的
            'port' => 6379,
            'database' => 0,
        ],
    ],
    'params' => $params,
    'modules'=> require(__DIR__.'/modules.php'),
];

defined('YII_DEBUG') or define('YII_DEBUG', false );
defined('YII_ENV') or define('YII_ENV', 'prod');

// defined('YII_DEBUG') or define('YII_DEBUG', strpos($_SERVER['SERVER_NAME'],'wintercoder.com') === false ? true : false);        ////怕效率慢
// defined('YII_ENV') or define('YII_ENV', strpos($_SERVER['SERVER_NAME'],'wintercoder.com') === false? 'dev' : 'prod');   //怕效率慢

// defined('YII_ENV') or define('YII_ENV', $_SERVER['SERVER_NAME'] != 'app.wintercoder.com' ? 'dev' : 'prod');  //单域名时用


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
