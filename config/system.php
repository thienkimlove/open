<?php
/**
 * Configuration for project.
 */

return [
    'generate_status' => [
        0 => 'Inactive',
        1 => 'Active'
    ],
    'user_status' => [
        0 => 'Chưa kích hoạt',
        1 => 'Kích hoạt'
    ],

    'insight' => [
        'types' => [
            'account' => 1,
            'campaign' => 2,
            'adset' => 3,
            'ad' => 4,
        ],
        'values' => [
            1 => 'Account',
            2 => 'Campaign',
            3 => 'AdSet',
            4 => 'Ad',
        ],
    ],

    'facebook' => [
        'app_id' => '234907703926',
        'app_secret' => '67bfb8ee4cb27f46f3a67de0ab40c976'
    ]




];
