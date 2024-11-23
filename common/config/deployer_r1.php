<?php
    return [
        'params' => [
            'platform' => [
                'frontendUrl' => '',
            ],
        ],
        'modules' => [
            'utility' => [
                'class' => 'open20\amos\utility\Module',
                'params' => [
                    'master_domain' => ''
                ]
            ]
        ],
        'params' => [
            'dev-mode' => true
        ]
    ];