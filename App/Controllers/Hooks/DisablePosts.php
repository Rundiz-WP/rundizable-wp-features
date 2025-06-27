<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisablePosts')) {
    /**
     * Disable posts class.
     * 
     * @since 0.2.7
     */
    class DisablePosts implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * Disable posts actions (admin). Redirect the user to admin dashboard.
         * 
         * @param \WP_Screen $current_screen
         */
        public function disablePosts(\WP_Screen $current_screen)
        {
            if (
                property_exists($current_screen, 'post_type') &&
                $current_screen->post_type === 'post' &&
                (
                    (
                        property_exists($current_screen, 'id') &&
                        (
                            $current_screen->id === 'edit-post' ||
                            $current_screen->id === 'edit-post_tag'
                        )
                    ) ||
                    (
                        property_exists($current_screen, 'action') &&
                        $current_screen->action === 'add'
                    )
                )
            ) {
                wp_safe_redirect(admin_url());
                exit();
            }

            if (
                property_exists($current_screen, 'id') &&
                (
                    $current_screen->id === 'options-writing'// Settings > Writing
                )
            ) {
                wp_safe_redirect(admin_url());
                exit();
            }
        }// disablePosts


        /**
         * Disable posts feed. Actually it will be disable all except comment feed. Couldn't find the way to disable only specific feed.
         * 
         * @param bool $is_comment_feed Whether the feed is a comment feed.
         * @param string $feed The feed name.
         */
        public function disablePostsFeed($is_comment_feed, $feed)
        {
            if (false === $is_comment_feed) {
                wp_die(__('Feed is unavailable.', 'rundizable-wp-feature'), 404);
            }
        }// disablePostsFeed


        /**
         * Disable posts actions (front). Redirect the user to home page.
         */
        public function disablePostsFront()
        {
            if (
                !defined('DOING_CRON') &&
                !is_attachment() &&
                !is_feed() &&
                (
                    is_single() ||
                    is_category() ||
                    is_tag() ||
                    is_archive()
                )
            ) {
                nocache_headers();
                wp_safe_redirect(home_url(), 301);
                exit();
            }
        }// disablePostsFront


        /**
         * Disable /posts endpoints in REST API.
         *
         * @param array $endpoints The original endpoints.
         * @return array Return removed endpoints.
         */
        public function disablePostsInRestApi($endpoints)
        {
            if (is_array($endpoints)) {
                foreach ($endpoints as $key => $value) {
                    if (is_scalar($key) && preg_match('/^\/wp\/v2\/posts/', $key)) {
                        unset($endpoints[$key]);
                    }
                }// endforeach;
                unset($key, $value);
            }

            return $endpoints;
        }// disablePostsInRestApi


        /**
         * Enqueue script or style to hide related settings.
         * 
         * @param string $hook_suffix
         */
        public function enqueScriptToHideRelatedSettings($hook_suffix)
        {
            if ('options-reading.php' === $hook_suffix) {
                wp_enqueue_style('rundizable-wp-feature-hide-posts-settings', plugin_dir_url(RUNDIZABLEWPFEATURES_FILE) . 'assets/css/admin/disable-posts-hide-related-settings.css', [], RUNDIZABLEWPFEATURES_VERSION);
            }
        }// enqueScriptToHideRelatedSettings


        /**
         * Hide posts metabox from menus management page.
         * 
         * @param array $hidden An array of IDs of hidden meta boxes.
         * @param \WP_Screen $screen WP_Screen object of the current screen.
         * @param bool $use_defaults Whether to show the default meta boxes. Default `true`.
         * @return array
         */
        public function hidePostsMetaBoxes(array $hidden, \WP_Screen $screen, $use_defaults)
        {
            if (!isset($screen->id) || !is_string($screen->id) || strtolower($screen->id) !== 'nav-menus') {
                return $hidden;
            }

            $hidden = array_merge($hidden, [
                'add-post-type-post',
                'add-category',
            ]);
            return $hidden;
        }// hidePostsMetaBoxes


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_posts']) && $rundizable_wp_features_optname['disable_posts'] == '1') {
                // admin bar
                add_action('wp_before_admin_bar_render', [$this, 'removeFromAdminBar']);
                // dashboard widgets
                add_action('wp_dashboard_setup', [$this, 'removeDashboardWidgets']);
                // disable its functional
                add_action('current_screen', [$this, 'disablePosts']);
                add_action('admin_menu', [$this, 'removePostsMenu']);
                add_filter('enable_post_by_email_configuration', '__return_false');
                add_filter('manage_users_columns', [$this, 'removePostsColumnOnUsersPage']);
                // hide related settings
                add_action('admin_enqueue_scripts', [$this, 'enqueScriptToHideRelatedSettings']);
                // admin menus management
                add_filter('hidden_meta_boxes', [$this, 'hidePostsMetaBoxes'], 10, 3);
                // widgets and block
                add_action('widgets_init', [$this, 'unregisterPostsWidgets']);
                add_filter('allowed_block_types_all', [$this, 'unregisterPostsBlocks'], 10, 2);
                // REST API
                add_filter('rest_endpoints', [$this, 'disablePostsInRestApi']);
            }

            if (isset($rundizable_wp_features_optname['disable_posts_front']) && $rundizable_wp_features_optname['disable_posts_front'] == '1') {
                add_action('template_redirect', [$this, 'disablePostsFront']);
                add_filter('the_posts', [$this, 'removePostsResult'], 10, 2);
                add_filter('get_categories_taxonomy', [$this, 'removeCategoriesResult'], 10, 2);
                add_filter('wp_tag_cloud', [$this, 'removeTagsResult'], 10, 2);
                // disable feed
                add_filter('feed_links_show_posts_feed', '__return_false');
                add_action('do_feed', [$this, 'disablePostsFeedx'], 9, 2);
                add_action('do_feed_atom', [$this, 'disablePostsFeed'], 9, 2);
                add_action('do_feed_rdf', [$this, 'disablePostsFeed'], 9, 2);
                add_action('do_feed_rss', [$this, 'disablePostsFeed'], 9, 2);
                add_action('do_feed_rss2', [$this, 'disablePostsFeed'], 9, 2);
            }
        }// registerHooks


        /**
         * Remove categories result.
         * 
         * @param string $taxonomy Taxonomy to retrieve terms from.
         * @param array $args An array of arguments. See get_terms().
         * @return string
         */
        public function removeCategoriesResult($taxonomy, $args)
        {
            if (is_admin()) {
                return $taxonomy;
            }

            if ($taxonomy === 'category') {
                return '';
            }

            return $taxonomy;
        }// removeCategoriesResult


        /**
         * Remove posts dashboard widgets.
         */
        public function removeDashboardWidgets()
        {
            remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        }// removeDashboardWidgets


        /**
         * Remove posts from admin bar.  
         * Admin bar is on the top of admin page where it contains WordPress logo, user profile icon.
         * 
         * @global \WP_Admin_Bar $wp_admin_bar
         */
        public function removeFromAdminBar()
        {
            /* @var $wp_admin_bar \WP_Admin_Bar */
            global $wp_admin_bar;

            $menusToRemove = [
                'new-post',
            ];

            foreach ($menusToRemove as $item) {
                $wp_admin_bar->remove_menu($item);
            }
        }// removeFromAdminBar


        /**
         * Remove posts column from Admin > Users page.
         * 
         * @param array $columns
         * @return array
         */
        public function removePostsColumnOnUsersPage(array $columns)
        {
            unset($columns['posts']);
            return $columns;
        }// removePostsColumnOnUsersPage


        /**
         * Remove Posts from admin menu.
         */
        public function removePostsMenu()
        {
            remove_menu_page('edit.php');
            // will not remove Tools > Available Tools in case WordPress publish some useful tools.
            remove_submenu_page('options-general.php', 'options-writing.php');// Settings > Writing
        }// removePostsMenu


        /**
         * Remove posts result.
         * 
         * @param WP_Post[] $posts Array of post objects.
         * @param WP_Query $query The WP_Query instance (passed by reference).
         */
        public function removePostsResult($posts, \WP_Query $query)
        {
            if (is_admin()) {
                return $posts;
            }

            if (is_iterable($posts) && !empty($posts)) {
                foreach ($posts as $post) {
                    if ($post->post_type === 'post') {
                        return [];
                    }
                    break;
                }// endforeach;
                unset($post);
            }// endif;

            return $posts;
        }// removePostsResult


        /**
         * Remove tags result.
         * 
         * @param string|array $return
         * @param array $args
         * @return mixed
         */
        public function removeTagsResult($return, array $args)
        {
            if (is_admin()) {
                return $tags;
            }

            if ('array' === $args['format']) {
                $return = [];
            } elseif ('flat' === $args['format']) {
                $return = '';
            } else {
                $return = null;
            }
            return $return;
        }// removeTagsResult


        /**
         * Unregister posts on blocks.
         * 
         * @link https://github.com/WordPress/gutenberg/issues/33730#issuecomment-1847253809 Code copied from here.
         * @param bool|array $allowed_block_types Array of block type slugs, or boolean to enable/disable all. Default `true` (all registered block types supported).
         * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
         * @return bool|array
         */
        public function unregisterPostsBlocks($allowed_block_types, \WP_Block_Editor_Context $block_editor_context)
        {
            if (!isset($block_editor_context->name)) {
                return $allowed_block_types;
            }

            if ($block_editor_context->name !== 'core/edit-widgets') {
                return $allowed_block_types;
            }

            if (!is_array($allowed_block_types)) {
                $registry = \WP_Block_Type_Registry::get_instance();
                $registerd_blocks = array_keys($registry->get_all_registered());
                $allowed_block_types = $registerd_blocks;
                unset($registerd_blocks, $registry);
            }// endif;

            $blocks_to_remove = [
                'core/archives',
                'core/categories',
                'core/latest-posts',
                'core/tag-cloud',
            ];
            $allowed_block_types = array_diff($allowed_block_types, $blocks_to_remove);
            $allowed_block_types = array_values($allowed_block_types);

            return $allowed_block_types;
        }// unregisterPostsBlocks


        /**
         * Un-register Posts widgets.
         */
        public function unregisterPostsWidgets()
        {
            unregister_widget('WP_Widget_Archives');
            unregister_widget('WP_Widget_Categories');
            unregister_widget('WP_Widget_Tag_Cloud');
            unregister_widget('WP_Widget_Recent_Posts');
        }// unregisterPostsWidgets

    
    }// DisablePosts
}// endif;
