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
            'campaign' => 2,
            'adset' => 3,
            'ad' => 4,
        ],
        'values' => [
            2 => 'Campaign',
            3 => 'AdSet',
            4 => 'Ad',
        ],

        'map' => [
            'VIDEO_VIEWS' => 'video_view',
            'LINK_CLICKS' => 'link_click',
            'MESSAGES' => 'link_click',
            'MULTIPLE' => 'link_click',
            'CONVERSIONS' => 'offsite_conversion.fb_pixel_add_to_cart',
        ]
    ],

    'facebook' => [
        'app_id' => '234907703926',
        'app_secret' => '67bfb8ee4cb27f46f3a67de0ab40c976'
    ],

    'social_type' => [
        'facebook' => 1,
    ],

    'social_type_values' => [
        1 => 'Facebook',
    ],




];
