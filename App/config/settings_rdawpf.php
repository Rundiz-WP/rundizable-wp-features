<?php
/** 
 * @package Rundizable-WP-Features
 */


$commonSelectOptions = [
    '0' => __('Enable', 'rundizable-wp-features'),
    '1' => __('Disable', 'rundizable-wp-features'),
];

return [
    'tab_style' => 'horizontal',
    'setting_tabs' => [
        [
            'icon' => 'fas fa-cogs',
            'title' => __('General', 'rundizable-wp-features'),
            'fields' => [
                [
                    'default' => '0',
                    'description' => __('Enable or disable posts for admin, menus management, widgets, dashboard widgets, REST API', 'rundizable-wp-features'). '<br>' . PHP_EOL .
                        __('This will remove all block about posts such as archives, latest posts, categories, tags from widgets.', 'rundizable-wp-features') . ' ' .
                        __('Exising blocks in widget areas will not be removed.', 'rundizable-wp-features'),
                    'id' => 'disable_posts',
                    'options' => $commonSelectOptions,
                    'title' => __('Posts', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// posts
                [
                    'default' => '0',
                    'description' => __('Enable or disable media for admin, widgets, REST API.', 'rundizable-wp-features') . '<br>' . PHP_EOL .
                        __('This will leave block editor untouch but image block will only work with URL. Existing media files will be able to select in the block.', 'rundizable-wp-features') . ' ' .
                        __('Exising blocks in widget areas will not be removed.', 'rundizable-wp-features'),
                    'id' => 'disable_media',
                    'options' => $commonSelectOptions,
                    'title' => __('Media', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// media
                [
                    'default' => '0',
                    'description' => __('Enable or disable comments on both admin and front-end (including feed), widgets, REST API.', 'rundizable-wp-features') . '<br>' . PHP_EOL .
                        __('This will remove all block about comments from widgets.', 'rundizable-wp-features') . ' ' .
                        __('Exising blocks in widget areas will not be removed.', 'rundizable-wp-features'),
                    'id' => 'disable_comments',
                    'options' => $commonSelectOptions,
                    'title' => __('Comments', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// comments
                [
                    'default' => '0',
                    'description' => __('Enable or disable pages for admin, menus management, block editor, widgets, REST API.', 'rundizable-wp-features') . '<br>' . PHP_EOL .
                        __('This will remove all block about pages from widgets.', 'rundizable-wp-features') . ' ' .
                        __('Exising blocks in widget areas will not be removed.', 'rundizable-wp-features'),
                    'id' => 'disable_pages',
                    'options' => $commonSelectOptions,
                    'title' => __('Pages', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// pages
                [
                    'default' => '0',
                    'description' => __('Disable XML-RPC entirely.', 'rundizable-wp-features'),
                    'id' => 'disable_xmlrpc',
                    'options' => $commonSelectOptions,
                    'title' => __('XML-RPC', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// xmlrpc
                [
                    'content' => '<hr>',
                    'type' => 'html',
                ],// hr separator
                [
                    'default' => '0',
                    'description' => __('If disabled, any URL in the front-end will be redirect to admin page. Feed will also disabled.', 'rundizable-wp-features'),
                    'id' => 'disable_frontend',
                    'options' => $commonSelectOptions,
                    'title' => __('Front-end', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// front-end
                [
                    'default' => '0',
                    'description' => sprintf(
                        /* translators: %1$s The Front-end setting name. */
                        __('Enable or disable posts for front-end, feed. If %1$s is disabled then this function is also disabled.', 'rundizable-wp-features'),
                        '<strong>' . __('Front-end', 'rundizable-wp-features') . '</strong>',
                    ),
                    'id' => 'disable_posts_front',
                    'options' => $commonSelectOptions,
                    'title' => __('Posts (front)', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// posts
                [
                    'default' => '0',
                    'description' => sprintf(
                        /* translators: %1$s The Front-end setting name. */
                        __('Enable or disable media for front-end. If %1$s is disabled then this function is also disabled.', 'rundizable-wp-features'),
                        '<strong>' . __('Front-end', 'rundizable-wp-features') . '</strong>',
                    ),
                    'id' => 'disable_media_front',
                    'options' => $commonSelectOptions,
                    'title' => __('Media (front)', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// media (front)
                [
                    'default' => '0',
                    'description' => sprintf(
                        /* translators: %1$s The Front-end setting name. */
                        __('Enable or disable pages for front-end. If %1$s is disabled then this function is also disabled.', 'rundizable-wp-features'),
                        '<strong>' . __('Front-end', 'rundizable-wp-features') . '</strong>',
                    ),
                    'id' => 'disable_pages_front',
                    'options' => $commonSelectOptions,
                    'title' => __('Pages (front)', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// pages (front)
            ],
        ],// end 1st tab
    ],
];