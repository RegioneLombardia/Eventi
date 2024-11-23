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
        'class' => 'open20\amos\core\components\AmosAssetManager',
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
                'sass' => [ // file extension to parse
                    'class' => 'nizsheanez\assetConverter\Sass',
                    'output' => 'css', // parsed output file type
                    'options' => [
                        'cachePath' => '@app/runtime/cache/sass-parser' // optional options
                    ],
                ],
                'scss' => [ // file extension to parse
                    'class' => 'open20\amos\core\converters\Scss',
                    'output' => 'css', // parsed output file type
                    'options' => [
//'sourceMap' => ['sourceRoot' => '@vendor/amos/planner/src/assets/web/node_modules/bootstrap-italia/src/scss/bootstrap-italia'],
                        'importPaths' => [
                            // '@vendor/amos/planner/src/assets/web/node_modules/bootstrap-italia/src/scss/bootstrap-italia',
                            '@vendor/open20/amos-layout/src/assets',
                            '@backend/web'
                        ], // import paths, you may use path alias here,
                        // e.g., `['@path/to/dir', '@path/to/dir1', ...]`
                        'enableCompass' => false,
                        'lineComments' => false, // if true — compiler will place line numbers in your compiled output
                        'outputStyle' => 'expanded', // May be `compressed`, `crunched`, `expanded` or `nested`,
                        // see more at http://sass-lang.com/documentation/file.SASS_REFERENCE.html#output_style
                    ],
                ],
                'less' => [ // file extension to parse
                    'class' => 'nizsheanez\assetConverter\Less',
                    'output' => 'css', // parsed output file type
                    'options' => [
                        'importDirs' => [], // import paths, you may use path alias here ex. '@app/assets/common/less'
                        'auto' => true, // optional options
                    ]
                ]
            ]
            /*'class' => 'cakebake\lessphp\AssetConverter',
            'compress' => true,
            'useCache' => false,
            //'cacheDir' => null,
            'cacheSuffix' => true,*/
        ],
    ],
    'eventSequence' => [
        'class' => '\raoul2000\workflow\events\BasicEventSequence',
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
                'enableCaching' => true,
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
                    'monitor' => '@backend/modules/monitor/i18n',
                    'amosmobilebridge' => '@vendor/open20/amos-mobile-bridge/src/modules/v1/i18n',

                ],
            ],
        ],
    ],
    /* 'translatemanager' => [
         'class' => 'lajax\translatemanager\Component'
     ],*/
    'authManager' => [
        'class' => 'open20\amos\core\rbac\DbManagerCached',
        'cache' => 'rbacCache'
    ],

    'urlManager' => [
        'class' => 'yii\web\UrlManager',
        // Disable index.php
        'showScriptName' => false,
        // Disable r= routes
        'enablePrettyUrl' => true,
        'rules' => array(
            '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>] => <module>/<controller>/<action>',
            '<language:[\w-]+>/<eventpage:[\w-]+>/amp => events/amp/event',
        ),
    ],
];