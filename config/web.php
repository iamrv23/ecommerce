<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'ecommerce-app',
    'name' => 'E-Commerce Platform',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'your-secret-key-here',
        ],
        'cache' => [
            'class' => 'yii\\caching\\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\\models\\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['auth/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\\swiftmailer\\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\\log\\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Admin routes
                'admin' => 'admin/index',
                'admin/<controller:\\w+>' => 'admin/<controller>/index',
                'admin/<controller:\\w+>/<action:\\w+>' => 'admin/<controller>/<action>',
                'admin/<controller:\\w+>/<action:\\w+>/<id:\\d+>' => 'admin/<controller>/<action>',

                // Customer routes
                'shop' => 'shop/index',
                'shop/category/<slug:[\\w-]+>' => 'shop/category',
                'shop/product/<slug:[\\w-]+>' => 'shop/product',
                'cart' => 'cart/index',
                'checkout' => 'checkout/index',
                'account' => 'account/index',
                'account/<action:\\w+>' => 'account/<action>',

                // Auth routes
                'login' => 'auth/login',
                'logout' => 'auth/logout',
                'register' => 'auth/register',
                'forgot-password' => 'auth/forgot-password',
                'reset-password/<token:[\\w-]+>' => 'auth/reset-password',

                // API routes
                'api/<controller:\\w+>/<action:\\w+>' => 'api/<controller>/<action>',
                'api/chatbot/<action:\\w+>' => 'api/chatbot/<action>',

                // Default
                '' => 'site/index',
                '<controller:\\w+>/<action:\\w+>' => '<controller>/<action>',
            ],
        ],
        'authManager' => [
            'class' => 'yii\\rbac\\DbManager',
            'cache' => 'cache',
        ],
        'formatter' => [
            'class' => 'yii\\i18n\\Formatter',
            'nullDisplay' => '',
            'currencyCode' => 'USD',
            'locale' => 'en-US',
        ],
        'assetManager' => [
            'bundles' => [
                'yii\\web\\JqueryAsset' => [
                    'sourcePath' => null,
                    'js' => [
                        'https://code.jquery.com/jquery-3.6.0.min.js',
                    ],
                ],
                'yii\\bootstrap5\\BootstrapAsset' => [
                    'css' => [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
                    ],
                ],
                'yii\\bootstrap5\\BootstrapPluginAsset' => [
                    'js' => [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
                    ],
                ],
            ],
        ],
        'session' => [
            'class' => 'yii\\web\\Session',
            'timeout' => 3600,
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\\debug\\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\\gii\\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
