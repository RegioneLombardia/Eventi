<?php
return [
    'view' => [
        'class' => 'open20\amos\core\components\AmosView',
        'theme' => [
            'pathMap' => [
                '@vendor/open20/amos-layout/src/views/layouts/parts/' => '@backend/modules/eventslayout/views/layouts/parts/',
                '@vendor/open20/amos-layout/src/views/layouts/fullsize/parts/' => '@backend/modules/eventslayout/views/layouts/fullsize/parts/',
                '@vendor/open20/amos-layout/src/views/layouts/fullsize/' => '@backend/modules/eventslayout/views/layouts/fullsize/',
            ],
        ],
    ],

    'rss' => [
        'class' => 'amos\rss\components\RssFeed',
    ]

];