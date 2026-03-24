<?php
/** 
 * @package Rundizable-WP-Features
 */


if (!defined('ABSPATH')) {
    exit();
}


$rundizable_wp_features_commonSelectOptions = [
    '0' => __('Enable', 'rundizable-wp-features'),
    '1' => __('Disable', 'rundizable-wp-features'),
];

if (
    (defined('DISALLOW_FILE_EDIT') || defined('DISALLOW_FILE_MODS')) &&
    !defined('RUNDIZABLEWPFEATURES_CUSTOM_DISALLOW_FILE_EDIT')
) {
    $rundizable_wp_features_disableFileEditorAdditionalDesc = ' ' . sprintf(
        /* translators: %1$s wp-config.php file */
        __('This setting is already defined in %1$s file.', 'rundizable-wp-features'),
        'wp-config.php'
    );
    if (
        (defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT === true) ||
        (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS === true)
    ) {
        $rundizable_wp_features_disableFileEditorDefault = '1';
    } else {
        $rundizable_wp_features_disableFileEditorDefault = '0';
    }
    $rundizable_wp_features_disableFileEditorInputAttributes = [
        'disabled' => 'disabled',
    ];
} else {
    $rundizable_wp_features_disableFileEditorDefault = '0';
    $rundizable_wp_features_disableFileEditorInputAttributes = [];
}

return [
    'tab_style' => 'horizontal',
    'setting_tabs' => [
        [
            'title' => __('General', 'rundizable-wp-features'),
            'fields' => [
                [
                    'default' => '0',
                    'description' => __('Enable or disable posts for admin, menus management, widgets, dashboard widgets, REST API', 'rundizable-wp-features') . '<br>' . PHP_EOL .
                        __('This will remove all block about posts such as archives, latest posts, categories, tags from widgets.', 'rundizable-wp-features') . ' ' .
                        __('Exising blocks in widget areas will not be removed.', 'rundizable-wp-features'),
                    'id' => 'disable_posts',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Posts', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// posts
                [
                    'default' => '0',
                    'description' => __('Enable or disable media for admin, widgets, REST API.', 'rundizable-wp-features') . '<br>' . PHP_EOL .
                        __('This will leave block editor untouch but image block will only work with URL. Existing media files will be able to select in the block.', 'rundizable-wp-features') . ' ' .
                        __('Exising blocks in widget areas will not be removed.', 'rundizable-wp-features'),
                    'id' => 'disable_media',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Media', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// media
                [
                    'default' => '0',
                    'description' => __('Enable or disable comments on both admin and front-end (including feed), widgets, REST API.', 'rundizable-wp-features') . '<br>' . PHP_EOL .
                        __('This will remove all block about comments from widgets.', 'rundizable-wp-features') . ' ' .
                        __('Exising blocks in widget areas will not be removed.', 'rundizable-wp-features'),
                    'id' => 'disable_comments',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Comments', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// comments
                [
                    'default' => '0',
                    'description' => __('Enable or disable pages for admin, menus management, block editor, widgets, REST API.', 'rundizable-wp-features') . '<br>' . PHP_EOL .
                        __('This will remove all block about pages from widgets.', 'rundizable-wp-features') . ' ' .
                        __('Exising blocks in widget areas will not be removed.', 'rundizable-wp-features'),
                    'id' => 'disable_pages',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Pages', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// pages
                [
                    'default' => '0',
                    'description' => __('Disable XML-RPC entirely.', 'rundizable-wp-features'),
                    'id' => 'disable_xmlrpc',
                    'options' => $rundizable_wp_features_commonSelectOptions,
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
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Front-end', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// front-end
                [
                    'default' => '0',
                    'description' => sprintf(
                        /* translators: %1$s The Front-end setting name. */
                        __('Enable or disable posts for front-end, feed. Disabled posts will be redirect to home page. If %1$s is disabled then this function is also disabled.', 'rundizable-wp-features'),
                        '<strong>' . __('Front-end', 'rundizable-wp-features') . '</strong>'
                    ),
                    'id' => 'disable_posts_front',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Posts (front)', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// posts
                [
                    'default' => '0',
                    'description' => sprintf(
                        /* translators: %1$s The Front-end setting name. */
                        __('Enable or disable media for front-end. Disabled media will be redirect to home page. If %1$s is disabled then this function is also disabled.', 'rundizable-wp-features'),
                        '<strong>' . __('Front-end', 'rundizable-wp-features') . '</strong>'
                    ),
                    'id' => 'disable_media_front',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Media (front)', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// media (front)
                [
                    'default' => '0',
                    'description' => sprintf(
                        /* translators: %1$s The Front-end setting name. */
                        __('Enable or disable pages for front-end. Disabled pages will be redirect to home page. If %1$s is disabled then this function is also disabled.', 'rundizable-wp-features'),
                        '<strong>' . __('Front-end', 'rundizable-wp-features') . '</strong>'
                    ),
                    'id' => 'disable_pages_front',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Pages (front)', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// pages (front)
                [
                    'default' => '0',
                    'description' => sprintf(
                        /* translators: %1$s The Front-end setting name. */
                        __('Enable or disable author page for front-end. Disabled author will be redirect to home page. If %1$s is disabled then this function is also disabled.', 'rundizable-wp-features'),
                        '<strong>' . __('Front-end', 'rundizable-wp-features') . '</strong>'
                    ),
                    'id' => 'disable_authorpage_front',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Author page (front)', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// author page
            ],
        ],// end 1st tab
        [
            'title' => __('Admin pages', 'rundizable-wp-features'),
            'fields' => [
                [
                    'default' => '0',
                    'description' => __('Enable or disable admin greeting next to the username.', 'rundizable-wp-features'),
                    'id' => 'disable_admin_greeting',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Admin greeting', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// howdy, greeting
                [
                    'default' => $rundizable_wp_features_disableFileEditorDefault,
                    'description' => __('Enable or disable plugin and theme file editor.', 'rundizable-wp-features') .
                        (isset($rundizable_wp_features_disableFileEditorAdditionalDesc) ? $rundizable_wp_features_disableFileEditorAdditionalDesc : ''),
                    'id' => 'disable_plugintheme_file_editor',
                    'input_attributes' => $rundizable_wp_features_disableFileEditorInputAttributes,
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Disable plugin and theme file editor', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// plugin and theme file editor
            ],
        ],// end 2nd tab
        [
            'title' => __('Users', 'rundizable-wp-features'),
            'fields' => [
                [
                    'default' => '0',
                    'description' => sprintf(
                        /* translators: %1$s User profile field, %2$s Users > Profile */
                        _x('Enable or disable %1$s in %2$s page.', 'user_profile', 'rundizable-wp-features'),
                        '<strong>' . __('Admin Color Scheme', 'rundizable-wp-features') . '</strong>',
                        '<strong>' . __('Users', 'rundizable-wp-features') . '&gt;' . __('Profile', 'rundizable-wp-features') . '</strong>'
                    ),
                    'id' => 'disable_users_profile_admin_color_scheme',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Admin Color Scheme', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// users > profile > admin color scheme
                [
                    'default' => '0',
                    'description' => sprintf(
                        /* translators: %1$s User profile field, %2$s Users > Profile */
                        _x('Enable or disable %1$s in %2$s page.', 'user_profile', 'rundizable-wp-features'),
                        '<strong>' . __('Website', 'rundizable-wp-features') . '</strong>',
                        '<strong>' . __('Users', 'rundizable-wp-features') . '&gt;' . __('Profile', 'rundizable-wp-features') . '</strong>'
                    ),
                    'id' => 'disable_users_profile_website',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Website', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// users > profile > website
                [
                    'default' => '0',
                    'description' => sprintf(
                        /* translators: %1$s User profile field, %2$s Users > Profile */
                        _x('Enable or disable %1$s in %2$s page.', 'user_profile', 'rundizable-wp-features'),
                        '<strong>' . __('Biographical Info', 'rundizable-wp-features') . '</strong>',
                        '<strong>' . __('Users', 'rundizable-wp-features') . '&gt;' . __('Profile', 'rundizable-wp-features') . '</strong>'
                    ),
                    'id' => 'disable_users_profile_biographical_info',
                    'options' => $rundizable_wp_features_commonSelectOptions,
                    'title' => __('Biographical Info', 'rundizable-wp-features'),
                    'type' => 'select',
                ],// users > profile > biographical info
            ],
        ],// end 3rd tab
    ],
];
