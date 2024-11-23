<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */
return [
    'errorHandler' => [
//        'errorAction' => 'layout/error/error',
        'errorAction' => 'site/error',
    ],
    'eventSequence' => [
        'class' => '\raoul2000\workflow\events\BasicEventSequence',
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
                'logVars' => ['_SERVER'],
            ],
        ],
    ],
    'request' => [
        'csrfParam' => '_csrf-backend',
        'enableCookieValidation' => true,
        'csrfCookie' => [
            'httpOnly' => true,
            'secure' => true
        ]
    ],
    'response' => [
        'class' => 'open20\amos\core\response\Response',
        'cspDirectives' => [
            'default-src' => "'self' 'unsafe-inline' google-analytics.com *.google-analytics.com cdn.jsdelivr.net *.jsdelivr.net fonts.googleapis.com *.googleapis.com fonts.gstatic.com *.gstatic.com *.g.doubleclick.net *.googleapis.com",
            'connect-src' => "'self' 'unsafe-inline' *.cloudfront.net *.ampproject.net connect.facebook.net www.googletagmanager.com google-analytics.com blob: google-analytics.com *.google-analytics.com cdn.jsdelivr.net *.jsdelivr.net fonts.googleapis.com *.googleapis.com *.g.doubleclick.net",
            'img-src' => "'self' 'unsafe-inline' https: data: blob: *.gstatic.com *.googleapis.com",
            'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' *.cloudfront.net ingestion.webanalytics.italia.it cdn.ampproject.org *.instagram.com www.youtube.com www.googletagmanager.com www.google.com/jsapi www.google.com/recaptcha/api.js www.gstatic.com *.jquery.com *.facebook.net maps.google.com *.cloudflare.com cdn.materialdesignicons.com google-analytics.com *.google-analytics.com cdn.jsdelivr.net *.jsdelivr.net fonts.googleapis.com *.googleapis.com *.fontawesome.com",
            'style-src' => "'self' 'unsafe-inline' www.gstatic.com *.cloudfront.net cdn.jsdelivr.net *.cloudflare.com cdn.materialdesignicons.com cdn.jsdelivr.net *.jsdelivr.net fonts.googleapis.com *.googleapis.com *.fontawesome.com",
            'font-src' => "'self' 'unsafe-inline' www.gstatic.com *.cloudfront.net cdn.jsdelivr.net *.fontawesome.com *.gstatic.com",
            'frame-src' => "'self' 'unsafe-inline' *.ampproject.net *.instagram.com *.google.com *.youtube.com *.regione.lombardia.it *.facebook.com",
            'manifest-src' => "'self' 'unsafe-inline' *.lcdn.com",
        ]
    ],
    'session' => [
        // this is the name of the session cookie used for login on the backend
        'class' => 'open20\amos\core\session\DBSession',
        'name' => 'advanced-backend',
        'cookieParams' => [
            'httpOnly' => true,
            'secure' => true
        ]
    ],
    'urlManager' => [
        'rules' => [
            'idm' => 'socialauthfe/shibboleth/set-module-instance',
            'internet' => 'socialauth/shibboleth/set-module-instance'
        ],
    ],
    'user' => [
        'class' => 'open20\amos\core\user\AmosUser',
        'identityClass' => 'open20\amos\core\user\User',
        'loginUrl' => '/admin/security/login',
        'enableAutoLogin' => true,
        'identityCookie' => [
            'name' => '_identity-backend',
            'httpOnly' => true,
            'secure' => true
        ],
    ],
    'socialShare' => [
        'class' => \open20\amos\core\components\ConfiguratorSocialShare::class,
    ],
];
