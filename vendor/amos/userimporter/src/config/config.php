<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\search
 * @category   CategoryName
 */

return [
    'config' => [
        
    ],
    'params' => [
        //active the search
        'searchParams' => [
            'user-import-task' => [
                'enable' => true,
            ],
            'user-import-edit-list' => [
                'enable' => true,
            ],
        ],
        //active the order
        'orderParams' => [
            'user-import-edit-list' => [
                'enable' => true,
                'fields' => [
                    'name',
                    'surname',
                    'email',
                ],
                'default_field' => 'id',
                'order_type' => SORT_DESC
            ]
        ],
    ]
];
