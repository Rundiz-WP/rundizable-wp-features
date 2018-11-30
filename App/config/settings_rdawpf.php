<?php
/** 
 * @package Rundizable-WP-Features
 */


return [
    'tab_style' => 'horizontal',
    'setting_tabs' => [
        [
            'icon' => 'fas fa-cogs',
            'title' => __('General', 'rundizable-wp-features'),
            'fields' => [
                [
                    'default' => '1',
                    'id' => 'disable_pages',
                    'options' => [
                        [
                            'title' => __('Disable', 'rundizable-wp-features'),
                            'value' => '1',
                        ],
                        [
                            'title' => __('Enable', 'rundizable-wp-features'),
                            'value' => '0',
                        ],
                    ],
                    'title' => __('Pages', 'rundizable-wp-features'),
                    'type' => 'radio',
                ],// page
                [
                    'default' => '1',
                    'id' => 'disable_media',
                    'options' => [
                        [
                            'title' => __('Disable', 'rundizable-wp-features'),
                            'value' => '1',
                        ],
                        [
                            'title' => __('Enable', 'rundizable-wp-features'),
                            'value' => '0',
                        ],
                    ],
                    'title' => __('Media', 'rundizable-wp-features'),
                    'type' => 'radio',
                ],// media
                [
                    'default' => '0',
                    'description' => __('If front-end is disabled, any URL in the front-end will be redirect to admin page.', 'rundizable-wp-features'),
                    'id' => 'disable_frontend',
                    'options' => [
                        [
                            'title' => __('Disable', 'rundizable-wp-features'),
                            'value' => '1',
                        ],
                        [
                            'title' => __('Enable', 'rundizable-wp-features'),
                            'value' => '0',
                        ],
                    ],
                    'title' => __('Front-end', 'rundizable-wp-features'),
                    'type' => 'radio',
                ],// front-end
            ],
        ],// end 1st tab
    ],
];