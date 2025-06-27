<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisableComments')) {
    /**
     * Disable comments class.
     * 
     * @since 0.2.7
     */
    class DisableComments implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * Disable comment actions (admin). Redirect the user to admin dashboard.
         * 
         * @param \WP_Screen $current_screen
         */
        public function disableComments(\WP_Screen $current_screen)
        {
            if (
                property_exists($current_screen, 'id') &&
                $current_screen->id === 'edit-comments' ||
                $current_screen->id === 'comment'
            ) {
                wp_safe_redirect(admin_url());
                exit();
            }
        }// disableComments


        /**
         * Disable comments feed.
         * 
         * @param bool $is_comment_feed Whether the feed is a comment feed.
         * @param string $feed The feed name.
         */
        public function disableCommentsFeed($is_comment_feed, $feed)
        {
            if (true === $is_comment_feed) {
                wp_die(__('Feed is unavailable.', 'rundizable-wp-feature'), 404);
            }
        }// disableCommentsFeed


        /**
         * Disable /comments endpoints in REST API.
         *
         * @param array $endpoints The original endpoints.
         * @return array Return removed endpoints.
         */
        public function disableCommentsInRestApi($endpoints)
        {
            if (is_array($endpoints)) {
                foreach ($endpoints as $key => $value) {
                    if (is_scalar($key) && preg_match('/^\/wp\/v2\/comments/', $key)) {
                        unset($endpoints[$key]);
                    }
                }// endforeach;
                unset($key, $value);
            }

            return $endpoints;
        }// disableCommentsInRestApi


        /**
         * Disable comments on all post types.
         */
        public function disableCommentsOnAllPostTypes()
        {
            $postTypes = get_post_types();
            foreach ($postTypes as $postType) {
                if (post_type_supports($postType, 'comments')) {
                    remove_post_type_support($postType, 'comments');
                }
                if (post_type_supports($postType, 'trackbacks')) {
                    remove_post_type_support($postType, 'trackbacks');
                }
            }// endforeach;
            unset($postType, $postTypes);
        }// disableCommentsOnAllPostTypes


        /**
         * Enqueue script or style to hide related settings.
         * 
         * @param string $hook_suffix
         */
        public function enqueScriptToHideRelatedSettings($hook_suffix)
        {
            if ('options-discussion.php' === $hook_suffix) {
                wp_enqueue_style('rundizable-wp-feature-hide-posts-settings', plugin_dir_url(RUNDIZABLEWPFEATURES_FILE) . 'assets/css/admin/disable-comments-hide-related-settings.css', [], RUNDIZABLEWPFEATURES_VERSION);
            }
        }// enqueScriptToHideRelatedSettings


        /**
         * Hide comments.
         * 
         * @param array $comments
         * @param int $post_id
         * @return array
         */
        public function hideComments(array $comments, $post_id)
        {
            return [];
        }// hideComments


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_comments']) && $rundizable_wp_features_optname['disable_comments'] == '1') {
                // disable its functional
                add_action('current_screen', [$this, 'disableComments']);
                add_action('admin_menu', [$this, 'removeCommentsMenu']);
                add_filter('comments_open', '__return_false');
                add_action('init', [$this, 'disableCommentsOnAllPostTypes']);
                // hide related settings
                add_action('admin_enqueue_scripts', [$this, 'enqueScriptToHideRelatedSettings']);
                // widgets and block
                add_action('widgets_init', [$this, 'unregisterCommentsWidgets']);
                add_filter('allowed_block_types_all', [$this, 'unregisterCommentsBlocks'], 10, 2);
                // REST API
                add_filter('rest_endpoints', [$this, 'disableCommentsInRestApi']);
                // hide comments on front-end
                add_filter('comments_array', [$this, 'hideComments'], 10, 2);
                add_filter('get_comments_number', [$this, 'setZeroCommentsNumber'], 10, 2);
                // disable comments on feed
                add_filter('feed_links_show_comments_feed', '__return_false');// disable comments feed link in the single post page.
                add_action('do_feed', [$this, 'disableCommentsFeed'], 9, 2);
                add_action('do_feed_atom', [$this, 'disableCommentsFeed'], 9, 2);
                add_action('do_feed_rdf', [$this, 'disableCommentsFeed'], 9, 2);
                add_action('do_feed_rss', [$this, 'disableCommentsFeed'], 9, 2);
                add_action('do_feed_rss2', [$this, 'disableCommentsFeed'], 9, 2);
            }
        }// registerHooks


        /**
         * Remove Comments from admin menu.
         */
        public function removeCommentsMenu()
        {
            remove_menu_page('edit-comments.php');
        }// removeCommentsMenu


        /**
         * Set zero to comments number.
         * 
         * @param int $comments_number
         * @param int $post_id
         * @return int
         */
        public function setZeroCommentsNumber($comments_number, $post_id)
        {
            return 0;
        }// setZeroCommentsNumber


        /**
         * Unregister comments on blocks.
         * 
         * @link https://github.com/WordPress/gutenberg/issues/33730#issuecomment-1847253809 Code copied from here.
         * @param bool|array $allowed_block_types Array of block type slugs, or boolean to enable/disable all. Default `true` (all registered block types supported).
         * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
         * @return bool|array
         */
        public function unregisterCommentsBlocks($allowed_block_types, \WP_Block_Editor_Context $block_editor_context)
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
                'core/comments',
                'core/latest-comments',
            ];
            $allowed_block_types = array_diff($allowed_block_types, $blocks_to_remove);
            $allowed_block_types = array_values($allowed_block_types);

            return $allowed_block_types;
        }// unregisterCommentsBlocks


        /**
         * Un-register Comment widgets.
         */
        public function unregisterCommentsWidgets()
        {
            unregister_widget('WP_Widget_Recent_Comments');
        }// unregisterCommentsWidgets


    }// DisableComments
}// endif;
