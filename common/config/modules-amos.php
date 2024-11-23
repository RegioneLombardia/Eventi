<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */
$modules['admin'] = [
    'class' => 'open20\amos\admin\AmosAdmin',
    'enableRegister' => true,
    'disableFirstAccesWizard' => true,
    'bypassWorkflow' => true,
    'disableUpdateChangeStatus' => true,
    'dontCheckOneTagPresent' => true,
    'tightCoupling' => true,
    'tightCouplingModel' => ['\open20\amos\events\models\EventGroupReferentMm' => 'event_group_referent_id'],
    'tightCouplingMethod' => ['\open20\amos\events\models\EventGroupReferent' => 'getTightCouplingGroups'],
    'enableValidationEmail' => true,
    'enableAttributeChangeLog' => [
        'nome',
        'cognome',
        'presentazione_breve',
        'sesso',
        'user_profile_age_group_id',
        'telefono',
        'azienda',
        'nascita_nazioni_id',
        'nascita_comuni_id',
        'nascita_province_id',
        'nascita_data',
        'codice_fiscale',
        'privacy',
        'privacy_2',
        'attivo',
    ],
    'tightCouplingRoleAdmin' => 'SUPER_USER_EVENT',
    'controllerMap' => [
        'user-profile' => [
            'class' => '\backend\modules\eventsadmin\controllers\UserProfileController',
        ],
        'default' => [
            'class' => '\backend\modules\eventsadmin\controllers\DefaultController',
        ],
        'security' => [
            'class' => '\backend\modules\eventsadmin\controllers\SecurityController',
        ],
    ],
    'modelMap' => [
        'UserProfileSearch' => 'backend\modules\eventsadmin\models\search\UserProfileSearch',
        'RegisterForm' => 'backend\modules\eventsadmin\models\RegisterForm',
        'CambiaPasswordForm' => 'backend\modules\eventsadmin\models\CambiaPasswordForm',
        'FirstAccessForm' => 'backend\modules\eventsadmin\models\FirstAccessForm',
    ],
    'profileRequiredFields' => [
        'nome',
        'cognome',
        'status',
    ],
    'fieldsConfigurations' => [
        'boxes' => [
            'box_account_data' => ['form' => true, 'view' => true],
            'box_dati_accesso' => ['form' => true, 'view' => true],
            'box_dati_contatto' => ['form' => true, 'view' => true],
            'box_dati_fiscali_amministrativi' => ['form' => true, 'view' => false],
            'box_dati_nascita' => ['form' => true, 'view' => true],
            'box_email_frequency' => ['form' => true, 'view' => false],
            'box_facilitatori' => ['form' => false, 'view' => false],
            'box_foto' => ['form' => true, 'view' => true],
            'box_informazioni_base' => ['form' => true, 'view' => true],
            'box_presentazione_personale' => ['form' => false, 'view' => false],
            'box_prevalent_partnership' => ['form' => false, 'view' => false],
            'box_privacy' => ['form' => true, 'view' => true],
            'box_questio' => ['form' => false, 'view' => false],
            'box_role_and_area' => ['form' => false, 'view' => false],
            'box_social_account' => ['form' => false, 'view' => true],
        ],
        'fields' => [
            'attivo' => ['form' => true, 'view' => true, 'referToBox' => 'box_account_data'],
            'codice_fiscale' => ['form' => true, 'view' => true, 'referToBox' => 'box_dati_fiscali_amministrativi'],
            'cognome' => ['form' => true, 'view' => true, 'referToBox' => 'box_informazioni_base'],
            'default_facilitatore' => ['form' => true, 'view' => true],
            'email' => ['form' => true, 'view' => false, 'referToBox' => 'box_dati_contatto'],
            'email_pec' => ['form' => false, 'view' => false, 'referToBox' => 'box_dati_contatto'],
            'facebook' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
            'facilitatore_id' => ['form' => false, 'view' => false, 'referToBox' => 'box_facilitatori'],
            'googleplus' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
            'linkedin' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
            'nascita_comuni_id' => ['form' => true, 'view' => true, 'referToBox' => 'box_dati_nascita'],
            'nascita_data' => ['form' => true, 'view' => true, 'referToBox' => 'box_dati_nascita'],
            'nascita_nazioni_id' => ['form' => true, 'view' => true, 'referToBox' => 'box_dati_nascita'],
            'nascita_province_id' => ['form' => true, 'view' => true, 'referToBox' => 'box_dati_nascita'],
            'nome' => ['form' => true, 'view' => true, 'referToBox' => 'box_informazioni_base'],
            'note' => ['form' => true, 'view' => false, 'referToBox' => 'box_informazioni_base'],
            'presentazione_breve' => ['form' => false, 'view' => false, 'referToBox' => 'box_informazioni_base'],
            'presentazione_personale' => ['form' => false, 'view' => false, 'referToBox' => 'box_presentazione_personale'],
            'prevalent_partnership_id' => ['form' => false, 'view' => false, 'referToBox' => 'box_prevalent_partnership'],
            'privacy' => ['form' => true, 'view' => true, 'referToBox' => 'box_privacy'],
            'sesso' => ['form' => true, 'view' => false, 'referToBox' => 'box_informazioni_base'],
            'telefono' => ['form' => true, 'view' => false, 'referToBox' => 'box_dati_contatto'],
            'twitter' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
            'google' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
            'ultimo_accesso' => ['form' => true, 'view' => true, 'referToBox' => 'box_account_data'],
            'ultimo_logout' => ['form' => true, 'view' => true, 'referToBox' => 'box_account_data'],
            'username' => ['form' => true, 'view' => false, 'referToBox' => 'box_dati_accesso'],
            'user_profile_age_group_id' => ['form' => true, 'view' => true, 'referToBox' => 'box_informazioni_base'],
            'user_profile_area_id' => ['form' => true, 'view' => false, 'referToBox' => 'box_role_and_area'],
            'userProfileImage' => ['form' => true, 'view' => true, 'referToBox' => 'box_foto'],
            'user_profile_role_id' => ['form' => true, 'view' => false, 'referToBox' => 'box_role_and_area'],
        ]
    ]
];

$modules['attachments'] = [
    'class' => 'open20\amos\attachments\FileModule',
    'webDir' => 'files',
    'tempPath' => '@common/uploads/temp',
    'storePath' => '@common/uploads/store',
    'cache_age' => '2592000',
    'disableGallery' => false,
    'disableFreeCropGallery' => true,
    'codiceTagGallery' => 'root_preference_center',
    'enableRequestImageForGallery' => true,
    'enableVirusScan' => false

];
$modules['comments'] = [
    'class' => 'open20\amos\comments\AmosComments',
    'modelsEnabled' => [
        'open20\amos\discussioni\models\DiscussioniTopic',
        'open20\amos\documenti\models\Documenti',
        'open20\amos\news\models\News',
    ],
];

$modules['comuni'] = [
    'class' => 'open20\amos\comuni\AmosComuni',
];
$modules['community'] = [
    'class' => 'open20\amos\community\AmosCommunity',
    'forceWorkflowSingleCommunity' => true,
    'disableEmailCommunityDeleted' => true
];

$modules['cmsbridge'] = [
    'class' => 'amos\cmsbridge\Module',
];

$modules['cwh'] = [
    'class' => 'open20\amos\cwh\AmosCwh',
    'cached' => false
];

$modules['documenti'] = [
    'class' => 'open20\amos\documenti\AmosDocumenti',
    'enableFolders' => true,
    'whiteListFilesExtensions' => 'csv, pdf, txt, doc, docx, xls, xlsx, rtf, pptx',
    'enableDocumentVersioning' => true,
];

$modules['elasticsearch'] = [
    'class' => '\open20\elasticsearch\Module',
    'hosts' =>  ['http://localhost:9205'],
    'useFinalSpecial' => false,
    'modelMap' => [
        'ElasticModelSearch' => 'common\modules\elasticsearch\ElasticModelSearch',
    ],
    'modelsEnabled' => [
        'open20\amos\events\models\Event' => 'common\modules\elasticsearch\EventTransformerManager',
    ],
    "foldingClass" => 'open20\elasticsearch\models\folding\ItalianFolding',
    'indexes_setting' => [
        'comunefe' => [
            "analysis" => [
                "analyzer" => [
                    "ope20_analyzer" => [
                        "type" => "custom",
                        "tokenizer" => "standard",
                        "char_filter" => [
                            "html_strip"
                        ],
                        "filter" => [
                            "lowercase",
                            "asciifolding"
                        ]
                    ]
                ]
            ]
        ],
        'italian' => [
            "analysis" => [
                "filter" => [
                    "italian_elision" => [
                        "type" => "elision",
                        "articles" => [
                            "c", "l", "all", "dall", "dell",
                            "nell", "sull", "coll", "pell",
                            "gl", "agl", "dagl", "degl", "negl",
                            "sugl", "un", "m", "t", "s", "v", "d"
                        ],
                        "articles_case" => true
                    ],
                    "italian_stop" => [
                        "type" => "stop",
                        "stopwords" => "_italian_"
                    ],
                    "italian_keywords" => [
                        "type" => "keyword_marker",
                        "keywords" => ["esempio"]
                    ],
                    "italian_stemmer" => [
                        "type" => "stemmer",
                        "language" => "light_italian"
                    ]
                ],
                "analyzer" => [
                    "open20_italian" => [
                        "tokenizer" => "standard",
                        "char_filter" => [
                            "html_strip"
                        ],
                        "filter" => [
                            "_ascii_folding" => [
                                "type" => "asciifolding",
                                "preserve_original" => true
                            ],
                            "italian_elision",
                            "lowercase",
                            "italian_stop",
                            "italian_keywords",
                            "italian_stemmer"
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$modules['email'] = [
    'class' => 'open20\amos\emailmanager\AmosEmail',
    'templatePath' => '@common/mail/emails',
];

$modules['elasticsearch'] = [
    'class' => '\open20\elasticsearch\Module',
    'hosts' =>  ['http://localhost:9205'],
    'useFinalSpecial' => false,
    'modelMap' => [
        'ElasticModelSearch' => 'common\modules\elasticsearch\ElasticModelSearch',
    ],
    'modelsEnabled' => [
        'open20\amos\events\models\Event' => 'common\modules\elasticsearch\EventTransformerManager',
    ],
    "foldingClass" => 'open20\elasticsearch\models\folding\ItalianFolding',
    'indexes_setting' => [
        'comunefe' => [
            "analysis" => [
                "analyzer" => [
                    "ope20_analyzer" => [
                        "type" => "custom",
                        "tokenizer" => "standard",
                        "char_filter" => [
                            "html_strip"
                        ],
                        "filter" => [
                            "lowercase",
                            "asciifolding"
                        ]
                    ]
                ]
            ]
        ],
        'italian' => [
            "analysis" => [
                "filter" => [
                    "italian_elision" => [
                        "type" => "elision",
                        "articles" => [
                            "c", "l", "all", "dall", "dell",
                            "nell", "sull", "coll", "pell",
                            "gl", "agl", "dagl", "degl", "negl",
                            "sugl", "un", "m", "t", "s", "v", "d"
                        ],
                        "articles_case" => true
                    ],
                    "italian_stop" => [
                        "type" => "stop",
                        "stopwords" => "_italian_"
                    ],
                    "italian_keywords" => [
                        "type" => "keyword_marker",
                        "keywords" => ["esempio"]
                    ],
                    "italian_stemmer" => [
                        "type" => "stemmer",
                        "language" => "light_italian"
                    ]
                ],
                "analyzer" => [
                    "open20_italian" => [
                        "tokenizer" => "standard",
                        "char_filter" => [
                            "html_strip"
                        ],
                        "filter" => [
                            "_ascii_folding" => [
                                "type" => "asciifolding",
                                "preserve_original" => true
                            ],
                            "italian_elision",
                            "lowercase",
                            "italian_stop",
                            "italian_keywords",
                            "italian_stemmer"
                        ]
                    ]
                ]
            ]
        ]
    ]
];


$modules['events'] = [
    'class' => 'open20\amos\events\AmosEvents',
    'restrictionRegisterDate' => false,
    'hideViewTabs' => ['tab-overview', 'tab-organization'],
    'enableNewWizard' => true,
    'disableSocial' => true,
    'enableFavorites' => false,
    'enableCustomMaps' => false,
    'enableLandingCalendar' => false,
    'enableOrderSection' => false,
    'groupReferentAdministration' => [
        'id' => 1,
        'role' => 'SUPER_USER_EVENT',
        'roleAdmin' => 'SUPER_USER'
    ],
    'operatorCandidate' => ['enabled' => true, 'role' => 'CANDIDATO_OPERATORE', 'courseid' => 9],
    'mailup_configurations' => [
        //'original_message_id' => 72,
        'original_message_id' => 21,
        'mailup_list_id' => 2,
        'dynamics_fields' => [
            1 => 'nome',
            2 => 'cognome',
            3 => 'azienda',
            4 => 'comune',
            5 => 'provincia',
            29 => 'codice_fiscale',
            30 => 'sesso',
            31 => 'eta',
            32 => 'user_id',
            33 => 'token',
        ]
    ],
    'eventsRequiredFields' => [
        'title',
        'description',
        'begin_date_hour',
        'event_type_id',
        'publish_in_the_calendar',
        'event_management',
        'event_commentable',
    ],
    'showOnlyRegionInWizard' => 3,
    'patronage_luya_nav_id' => 395, //pre-prod
    // 'patronage_luya_nav_id' => 377, /* test*/
    'default_image_patronage' => '/img/blue_image.jpg',
    'user_profile_privacy_attr' => 'privacy_2',
    'url_download_app_mobile' => "https://play.google.com/store/apps/details?id=it.lispa.sire.app.mobile.poi&hl=it&gl=US",
    'enabled_dg_configurations' => [
        'poi_integration' => [1],              // DG UO Comunicazione
        'giovani_platform_integration' => [4], // DG Sport e Giovani
    ]

];
$modules['favorites'] = [
    'class' => 'open20\amos\favorites\AmosFavorites',
    'enableFavoritesUrl' => true,
    'modelsEnabledFavoritesUrl' => [
        'open20\amos\events\models\Event',
    ]
];

$modules['mobilebridge'] = [
    'class' => 'open20\amos\mobile\bridge\Module',
    'modules' => [
        'v1' => [
            'controllerMap' => [
                'event' => 'open20\amos\mobile\bridge\modules\v1\controllers\EventPlatformController',
                'share' => 'open20\amos\mobile\bridge\modules\v1\controllers\ShareEventPlatformController',
            ],
        ],
    ],
];

$modules['moodle'] = [
    'class' => 'open20\amos\moodle\AmosMoodle',
    'secretKey' => 'xxx',
    'adminUsername' => 'xxxx',
    'moodleOpen20baseRoleId' => 9,
    'moodleUrl' => "https://example.it",
    'moodleAdministratorToken' => "xxxxxx",
    'disableEnrolmentEmail' => true
];

$modules['news'] = [
    'class' => 'open20\amos\news\AmosNews',
];

$modules['newsletter'] = [
    'class' => 'amos\newsletter\Module',
    'client_id' => "xxxxxxxx",
    'client_secret' => "xxxxxxxxx",
    'customModel' => '',
    'username' => "xxxxxxx",
    'password' => "xxxxxxxx",
    'SMTP_username' => 'xxxxxxxx',
    'SMTP_password' => 'xxxxxxxxx',
];


$modules['notify'] = [
    'class' => 'open20\amos\notificationmanager\AmosNotify',
    'contentToNotNotify' => ['open20\amos\events\models\Event']

];

$modules['seo'] = [
    'class' => 'open20\amos\seo\AmosSeo',
    'modelsEnabled' => [
        'open20\amos\discussioni\models\DiscussioniTopic',
        'open20\amos\events\models\Event',
        'open20\amos\news\models\News',
        'open20\amos\community\models\Community',
        'open20\amos\documenti\models\Documenti',
    ],
];


$modules['translation'] = [
    'class' => 'open20\amos\translation\AmosTranslation',
    'queryCache' => 'translateCache',
    'cached' => true,
    'translationBootstrap' => [
        'configuration' => [
            'translationLabels' => [
                'classBehavior' => \lajax\translatemanager\behaviors\TranslateBehavior::className(),
                'models' => [
                    [
                        'namespace' => 'cornernote\workflow\manager\models\Status',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'lajax\translatemanager\behaviors\TranslateBehavior'
                        'attributes' => ['label'],
                    ],
                ],
            ],
            'translationContents' => [
                'classBehavior' => \open20\amos\translation\behaviors\TranslateableBehavior::className(),
                'models' => [
                    /* [
                      'namespace' => 'cornernote\workflow\manager\models\Status',
                      //'connection' => 'db', //if not set it use 'db'
                      //'classBehavior' => NULL,//if not set it use default classBehavior 'lajax\translatemanager\behaviors\TranslateBehavior'
                      'enableWorkflow' => FALSE,
                      'attributes' => ['label'],
                      'plugin' => 'workflow-manager',
                      ], */
                    ['namespace' => 'open20\amos\tag\models\Tag',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['nome', 'descrizione'],
                        'plugin' => 'tag'
                    ],
                    ['namespace' => 'open20\amos\news\models\NewsCategorie',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['titolo', 'sottotitolo'],
                        'plugin' => 'news'
                    ],
                    ['namespace' => 'open20\amos\documenti\models\DocumentiCategorie',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['titolo', 'descrizione'],
                        'plugin' => 'documenti'
                    ],
                    ['namespace' => 'open20\amos\community\models\CommunityType',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['name', 'description'],
                        'plugin' => 'community'
                    ],
                    ['namespace' => 'open20\amos\events\models\Event',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['title', 'summary', 'description'],
                        'plugin' => 'events'
                    ],
                    ['namespace' => 'open20\amos\events\models\EventLanding',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['schedule', 'description', 'contact_info_organizator', 'text_sending_memo', 'thank_you_subscribe', 'thank_you_registered', 'thank_you_waiting_list',
                            'share_title','rating_description', 'rating_title','title_instagram_video','title_related_events','title_protagonists','title_pics','title_video','title_info','title_documents','title_request_info','title_schedule','title_presentation','title_maps', 'description_protagonists',
                            'streaming_url'],
                        'plugin' => 'events'
                    ],
                    ['namespace' => 'open20\amos\events\models\EventLocationEntrances',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['name', 'description'],
                        'plugin' => 'events'
                    ],
                    ['namespace' => 'open20\amos\events\models\EventLocation',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['name', 'description'],
                        'plugin' => 'events'
                    ],

                    ['namespace' => 'open20\amos\events\models\EventEmailTemplates',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['save_the_date', 'registration_email',
                            'confirm_registration', 'info_waiting_list',
                            'webmeeting_webex', 'webmeeting_webex_confirm_reg',
                            'webmeeting_webex_save_date', 'send_tickets'],
                        'plugin' => 'events'
                    ],
                    ['namespace' => 'open20\amos\events\models\EventCommunication',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['text_email', 'title'],
                        'plugin' => 'events'
                    ],
                    ['namespace' => 'open20\amos\news\models\News',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['titolo', 'sottotitolo', 'descrizione', 'descrizione_breve'],
                        'plugin' => 'news'
                    ],
                    ['namespace' => 'open20\amos\documenti\models\Documenti',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['titolo', 'sottotitolo', 'descrizione', 'descrizione_breve'],
                        'plugin' => 'documenti'
                    ],
                    ['namespace' => 'open20\amos\discussioni\models\DiscussioniTopic',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['titolo', 'testo'],
                        'plugin' => 'discussioni'
                    ],
                    ['namespace' => 'amos\sitemanagement\models\SiteManagementSliderElem',
                        //'connection' => 'db', //if not set it use 'db'
                        //'classBehavior' => NULL,//if not set it use default classBehavior 'open20\amos\translation\behaviors\TranslateableBehavior'
                        'enableWorkflow' => FALSE, //if not set it use default configuration of the plugin
                        'attributes' => ['title'],
                        'plugin' => 'sitemanagement'
                    ],
                ],
            ],
        ],
    ],
    'module_translation_labels' => 'translatemanager',
    'module_translation_labels_options' => ['roles' => ['ADMIN', 'CONTENT_TRANSLATOR'],
        'root' => [
            '@vendor/amos/',
            '@vendor/',
            '@app',
            '@backend',
            '@frontend',
            '@vendor/open20/',
        ],
    ], //all the options of the translatemanager
    'components' => [
        'translatemanager' => [
            'class' => 'lajax\translatemanager\Component'
        ],
    ],
    'defaultLanguage' => 'it-IT',
    'defaultUserLanguage' => 'it-IT',
    'pathsForceTranslation' => ['*'],
    'enableLabelTranslationField' => true,
    'byPassPermissionInlineTranslation' => true,
    'labelTranslationField' => '<span class="badge badge-pill badge-outline-secondary ml-2" data-toggle="tooltip" title="{altTranslation} {translation}"><span class="label_translation am am-translate"></span> - {translation}</span>'

];

$modules['tickets'] = [
    'class' => 'backend\modules\tickets\Module',
];

$modules['socialauth'] = [
    'class' => 'open20\amos\socialauth\Module',
    'enableLogin' => true,
    'enableLink' => false,
    'enableRegister' => true,
    'enableSpid' => true,
    'userOverload' => true,
    'shibbolethAutoRegistration' => true,
    'disableAssociationByEmail' => true,
    'providers' => [
        /* "Google" => [ //socialopen20@gmail.com
             "enabled" => true,
             "keys" => [
                 "id" => "xxxxxxx.apps.googleusercontent.com",
                 "secret" => "xxxxxxx",
                 "scope" => "https://www.googleapis.com/auth/userinfo.profile",
                 "https://www.googleapis.com/auth/userinfo.email",
             ],
         ],
         "Apple" => [
             "enabled" => true,
             "keys" => [
                 "id" => "it.eventilombardia.signin",
                 "team_id" => "xxxxxxx",
                 "key_id" => "xxxxxxx",
                 "key_file" => '/var/www/stage-hosts/example.it/httpdocs/common/uploads/apple/xxxxxxxx.p8'
             ],
             "scope" => "name email",
             "verifyTokenSignature" => false
         ],
         "Facebook" => [//
             "enabled" => true,
             "keys" => [
                 "id" => "xxxxxxx",
                 "secret" => "xxxxxxxx"
             ],
             "scope" => "email"
         ],
         "Twitter" => [//elitedivsocial
             "enabled" => true,
             "keys" => [
                 "key" => "xxxxxxxx",
                 "secret" => "xxxxxxxxx"
             ],
             "scope" => 'email',
             "includeEmail" => true
         ],
         "LinkedIn" => [
             "enabled" => true,
             "keys" => [
                 "id" => "xxxxxxx",
                 "secret" => "xxxxxxxx"
             ],
             "state" => md5(time()),
             "scope" => "r_liteprofile r_emailaddress w_member_social",
             //"fields"  => array("id", "email-address", "first-name", "last-name")
         ],*/
    ]
];

$modules['socialauthfe'] = [
    'class' => 'open20\amos\socialauth\Module',
    'enableLogin' => true,
    'enableLink' => false,
    'enableRegister' => true,
    'enableSpid' => true,
    'userOverload' => true,
    'shibbolethAutoRegistration' => true,
    'disableAssociationByEmail' => true,
    'providers' => [
    ]
];


$modules['tag'] = [
    'class' => 'open20\amos\tag\AmosTag',
];

$modules['userimporter'] = [
    'class' => 'amos\userimporter\Module',
    'url_example_file' => '/files/user_import_example.xlsx',
];

$modules['webmeeting'] = [
    'class' => 'open20\webmeeting\WebMeetingModule',
    'providers' => [
        'WebEx' => [
            // Location where to redirect users once they authenticate with a provider
            'callback' => 'https://events-pre-prod.stage.demotestwip.it/webmeeting/web-meeting/auth',
            'enabled' => true, // Optional: indicates whether to enable or disable WebEx adapter. Defaults to false
            'keys' => [
                'key' => 'C2cc1cfaa0098ac25bdee4c2b864273352d5ea69a4db42b11966862fa1967e118',
                'secret' => 'aa0448e7c6fc5556b9c70f9c9893876ba304b18cd053ce63a5f863a9864817e9',
            ]
        ],
    ]
];

return $modules;
