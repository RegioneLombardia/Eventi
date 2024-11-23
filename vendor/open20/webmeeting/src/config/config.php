<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Configs
 */

return [
    'params' => [
        'searchParams' => [
            'web-meeting' => [
                'enable' => true,
            ],
            'web-meeting-team' => [
                'enable' => true,
            ],
            'web-meeting-invitee' => [
                'enable' => true,
            ],
            'web-meeting-history' => [
                'enable' => true,
            ],
        ],

        //active the order
        'orderParams' => [
            'web-meeting' => [
                'enable' => true,
                'fields' => [
                    'id',
                    'title',
                    'description',
                ],
                'default_field' => 'id',
                'order_type' => SORT_DESC
            ],
            'web-meeting-team' => [
                'enable' => true,
                'fields' => [
                    'id',
                    'title',
                    'description',
                ],
                'default_field' => 'id',
                'order_type' => SORT_DESC
            ]

        ],
    ]
];