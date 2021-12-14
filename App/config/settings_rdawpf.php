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
                    'description' => __('Enable or disable pages for admin management, menu, widgets, REST API.', 'rundizable-wp-features'),
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
                    'description' => __('Enable or disable media for admin management, menu, widgets, REST API.', 'rundizable-wp-features'),
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
                [
                    'default' => '1',
                    'description' => __('Enable or disable pages for front-end. If front-end is disabled then this function is also disabled.', 'rundizable-wp-features'),
                    'id' => 'disable_pages_front',
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
                    'title' => __('Pages (front)', 'rundizable-wp-features'),
                    'type' => 'radio',
                ],// page (front)
                [
                    'default' => '1',
                    'description' => __('Enable or disable media for front-end. If front-end is disabled then this function is also disabled.', 'rundizable-wp-features'),
                    'id' => 'disable_media_front',
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
                    'title' => __('Media (front)', 'rundizable-wp-features'),
                    'type' => 'radio',
                ],// media (front)
            ],
        ],// end 1st tab
    ],
];