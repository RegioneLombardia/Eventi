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
    'assetManager' => [
        'appendTimestamp' => true,
        'forceCopy' => false,
        'hashCallback' => function ($path) {
            return hash('md4', $path);
        },

        'converter' => [
            'class' => 'nizsheanez\assetConverter\Converter',
            'force' => false,
            'destinationDir' => 'compiled', //at which folder of @webroot put compiled files
            'parsers' => [
                'sass' => [// file extension to parse
                    'class' => 'nizsheanez\assetConverter\Sass',
                    'output' => 'css', // parsed output file type
                    'options' => [
                        'cachePath' => '@app/runtime/cache/sass-parser' // optional options
                    ],
                ],
                'scss' => [// file extension to parse
                    'class' => 'open20\amos\core\converters\Scss',
                    'output' => 'css', // parsed output file type
                    'options' => [
                        'importPaths' => [
                            '@vendor/open20/design/src/assets',
                            '@common/web'
                        ], // import paths, you may use path alias here,
                        // e.g., `['@path/to/dir', '@path/to/dir1', ...]`
                        'enableCompass' => false,
                        'lineComments' => false, // if true — compiler will place line numbers in your compiled output
                        'outputStyle' => 'expanded', // May be `compressed`, `crunched`, `expanded` or `nested`,
                        // see more at http://sass-lang.com/documentation/file.SASS_REFERENCE.html#output_style
                    ],
                ],
                'less' => [// file extension to parse
                    'class' => 'nizsheanez\assetConverter\Less',
                    'output' => 'css', // parsed output file type
                    'options' => [
                        'importDirs' => [], // import paths, you may use path alias here ex. '@app/assets/common/less'
                        'auto' => true, // optional options
                    ]
                ]
            ]
        ],
        'bundles' => [
            'dosamigos\google\maps\MapAsset' => [
                'options' => [
                    'key' => '',
                    'version' => '3.1.18'
                ]
            ]
        ]
    ],
    'authManager' => [
        'class' => 'open20\amos\core\rbac\DbManagerCached',
        'cache' => 'rbacCache'
    ],
    'composition' => [
        'hidden' => false, // no languages in your url (most case for pages which are not multi lingual)
        'pattern' => '<langShortCode:[a-z]{2}>',
        'default' => ['langShortCode' => 'it'], // the default language for the composition should match your default language shortCode in the language table.
    ],
    'errorHandler' => [
        // 'class' => 'app\modules\cms\error\ErrorHandler',
    ],
    'i18n' => [
        'translations' => [
            '*' => [
                'class' => 'open20\amos\core\i18n\MessageSource',
                'db' => 'db',
                'sourceLanguage' => 'it-IT', // Developer language
                'sourceMessageTable' => '{{%language_source}}',
                'messageTable' => '{{%language_translate}}',
                'cachingDuration' => 86400,
                'enableCaching' => false,
                'forceTranslation' => true,
                'autoUpdate' => true,
                'extraCategoryPaths' => [
                    'amoscore' => '@vendor/open20/amos-core/i18n',
                    'amos' => '@common/translation/amos/i18n',
                    'amosplatform' => '@common/translation/amosplatform/i18n',
                    'amosapp' => '@common/translation/amosapp/i18n',
                    'cruds' => '@common/translation/cruds/i18n',
                    'giiamos' => '@common/translation/giiamos/i18n',
                    'javascript' => '@common/translation/javascript/i18n',
                    'site' => '@common/translation/site/i18n',
                    'wizard' => '@common/translation/wizard/i18n',
                ],
            ],
        ],
    ],
    'breadcrumbcache' => [
        'class' => 'yii\caching\FileCache',
        'cachePath' => '@runtime/breadcrumbcache'
    ],
    'cache' => [
        // 'class' => 'yii\caching\FileCache',
        'class' => 'yii\caching\DummyCache',
    ],
    'landingCache' => [
        'class' => 'yii\caching\FileCache',
        'cachePath' => '@runtime/landingCache'
    ],
    'assetscache' => [
        'class' => 'yii\caching\FileCache',
        'cachePath' => '@runtime/assetsCache'
    ],
    'rbacCache' => [
        'class' => 'yii\caching\FileCache',
        'cachePath' => '@runtime/rbacCache'
    ],
    'schemaCache' => [
        'class' => 'yii\caching\FileCache',
        'cachePath' => '@runtime/schemaCache'
    ],
    'translateCache' => [
        'class' => 'yii\caching\DummyCache',
        //'cachePath' => '@runtime/translateCache'
    ],
//    'request' => [
//        'enableCookieValidation' => false
////      'csrfParam' => '_csrf-frontend',
//    ],

    /* 'session' => [
      'class' => 'yii\web\CacheSession',
      // this is the name of the session cookie used for login on the frontend
      'name' => 'advanced-frontend',
      ], */
    'storage' => [
        'class' => 'app\modules\cms\storage\AmosFileSystem'
    ],
    'translatemanager' => [
        'class' => 'lajax\translatemanager\Component'
    ],
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
//        'hostInfo' => '',
        'rules' => [
            'site/login' => 'site/login',
            'site/logout' => 'site/logout',
            'esplora-eventi/amp' => 'ampevent/amp/esplora-eventi',
            'grattacielo-pirelli/amp' => 'ampevent/amp/grattacielo-pirelli',
            'palazzo-lombardia/amp' => 'ampevent/amp/palazzo-lombardia',
            '<eventpage:[\w-]+>/amp' => 'ampevent/amp/event',
            'amp' => 'ampevent/amp/esplora-eventi'
        ]
    ],
    'view' => [
        'class' => 'app\modules\cms\base\CmsView',
        'theme' => [
            'pathMap' => [
                '@vendor/trk/luya-uikit/src/views/blocks' => '@frontend/modules/uikit/views',
            ],
        ],
    ],
    'adminuser' => [
        'class' => 'app\modules\cms\components\AdminUser',
        'defaultLanguage' => 'it',
        'enableAutoLogin' => true,
        /* 'identityCookie' => [
          'name' => '_identity-luya',
          'httpOnly' => true,
          'secure' => true,
          'domain' => ".demotestwip.it",
          ], */
    ],
//    'user' => [
//        'class' => 'open20\amos\core\user\AmosUser',
//        'identityClass' => 'open20\amos\core\user\User',
//        'identityCookie' => [
//            'enableAutoLogin' => true,
//            'name' => '_identity',
//            'path'  => '/',
//            'httpOnly' => true,
//            'domain' => ".devel.demotestwip.it",
//        ]
//    ],
    'response' => [
        'class' => 'open20\amos\core\response\Response',
        'cspDirectives' => [
            'default-src' => "'self' 'unsafe-inline' *.regione.lombardia.it google-analytics.com *.google-analytics.com cdn.jsdelivr.net *.jsdelivr.net fonts.googleapis.com *.googleapis.com fonts.gstatic.com *.gstatic.com *.g.doubleclick.net *.googleapis.com",
            'connect-src' => "'self' 'unsafe-inline' *.ampproject.net connect.facebook.net www.googletagmanager.com google-analytics.com blob: google-analytics.com *.google-analytics.com cdn.jsdelivr.net *.jsdelivr.net fonts.googleapis.com *.googleapis.com *.g.doubleclick.net",
            'img-src' => "'self' 'unsafe-inline' https: data: blob: *.gstatic.com *.googleapis.com",
            'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' ingestion.webanalytics.italia.it d13yocgth49rto.cloudfront.net cdn.ampproject.org *.instagram.com www.youtube.com www.googletagmanager.com www.google.com/jsapi www.google.com/recaptcha/api.js www.gstatic.com *.jquery.com *.facebook.net maps.google.com *.cloudflare.com cdn.materialdesignicons.com google-analytics.com *.google-analytics.com cdn.jsdelivr.net *.jsdelivr.net fonts.googleapis.com *.googleapis.com *.fontawesome.com",
            'style-src' => "'self' 'unsafe-inline' d13yocgth49rto.cloudfront.net www.gstatic.com cdn.jsdelivr.net *.cloudflare.com cdn.materialdesignicons.com cdn.jsdelivr.net *.jsdelivr.net fonts.googleapis.com *.googleapis.com *.fontawesome.com",
            'font-src' => "'self' 'unsafe-inline' d13yocgth49rto.cloudfront.net www.gstatic.com cdn.jsdelivr.net *.fontawesome.com *.gstatic.com",
            'frame-src' => "'self' 'unsafe-inline' player.flipsnack.com *.ampproject.net *.instagram.com *.google.com *.youtube.com *.regione.lombardia.it *.facebook.com",
            'manifest-src' => "'self' 'unsafe-inline' *.lcdn.com",
        ]
    ],
    'socialShare' => [
        'class' => \open20\amos\core\components\ConfiguratorSocialShare::class,
    ]

];
