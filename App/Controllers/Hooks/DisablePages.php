<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisablePages')) {
    class DisablePages implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * Disable pages actions (admin). Redirect the user to admin dashboard.
         * 
         * @param \WP_Screen $current_screen
         */
        public function disablePages(\WP_Screen $current_screen)
        {
            if (
                is_object($current_screen) &&
                property_exists($current_screen, 'post_type') &&
                $current_screen->post_type === 'page' &&
                (
                    (
                        property_exists($current_screen, 'id') &&
                        $current_screen->id === 'edit-page'
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
        }// disablePages


        /**
         * Disable pages actions (front). Redirect the user to home page.
         */
        public function disablePagesFront()
        {
            if (
                !defined('DOING_CRON') &&
                is_page()
            ) {
                nocache_headers();
                wp_safe_redirect(home_url(), 301);
                exit();
            }
        }// disablePagesFront


        /**
         * Disable /pages endpoints in REST API.
         *
         * @param array $endpoints The original endpoints.
         * @return array Return removed endpoints.
         */
        public function disablePagesInRestApi($endpoints)
        {
            if (is_array($endpoints)) {
                foreach ($endpoints as $key => $value) {
                    if (is_scalar($key) && preg_match('/^\/wp\/v2\/pages/', $key)) {
                        unset($endpoints[$key]);
                    }
                }// endforeach;
                unset($key, $value);
            }

            return $endpoints;
        }// disablePagesInRestApi


        /**
         * Hide pages metabox from menus management page.
         * 
         * @since 0.2.7
         * @param array $hidden An array of IDs of hidden meta boxes.
         * @param \WP_Screen $screen WP_Screen object of the current screen.
         * @param bool $use_defaults Whether to show the default meta boxes. Default `true`.
         * @return array
         */
        public function hidePagesMetaBoxes(array $hidden, \WP_Screen $screen, $use_defaults)
        {
            if (!isset($screen->id) || !is_string($screen->id) || strtolower($screen->id) !== 'nav-menus') {
                return $hidden;
            }

            $hidden = array_merge($hidden, [
                'add-post-type-page',
            ]);
            return $hidden;
        }// hidePagesMetaBoxes


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_pages']) && $rundizable_wp_features_optname['disable_pages'] == '1') {
                // admin bar
                add_action('wp_before_admin_bar_render', [$this, 'removeFromAdminBar']);
                // disable its functional
                add_action('current_screen', [$this, 'disablePages']);
                add_action('admin_menu', [$this, 'removePagesMenu']);
                // admin menus management
                add_filter('hidden_meta_boxes', [$this, 'hidePagesMetaBoxes'], 10, 3);
                // widgets and block
                add_action('widgets_init', [$this, 'unregisterPagesWidgets']);
                add_filter('allowed_block_types_all', [$this, 'unregisterPagesBlocks'], 10, 2);
                // REST API
                add_filter('rest_endpoints', [$this, 'disablePagesInRestApi']);
            }

            if (isset($rundizable_wp_features_optname['disable_pages_front']) && $rundizable_wp_features_optname['disable_pages_front'] == '1') {
                add_action('template_redirect', [$this, 'disablePagesFront']);
            }
        }// registerHooks


        /**
         * Remove pages from admin bar.  
         * Admin bar is on the top of admin page where it contains WordPress logo, user profile icon.
         * 
         * @since 0.2.7
         * @global \WP_Admin_Bar $wp_admin_bar
         */
        public function removeFromAdminBar()
        {
            /* @var $wp_admin_bar \WP_Admin_Bar */
            global $wp_admin_bar;

            $menusToRemove = [
                'new-page',
            ];

            foreach ($menusToRemove as $item) {
                $wp_admin_bar->remove_menu($item);
            }
        }// removeFromAdminBar


        /**
         * Remove Pages from admin menu.
         */
        public function removePagesMenu()
        {
            remove_menu_page('edit.php?post_type=page');
        }// removePagesMenu


        /**
         * Unregister pages on blocks.
         * 
         * @since 0.2.7
         * @link https://github.com/WordPress/gutenberg/issues/33730#issuecomment-1847253809 Code copied from here.
         * @param bool|array $allowed_block_types Array of block type slugs, or boolean to enable/disable all. Default `true` (all registered block types supported).
         * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
         * @return bool|array
         */
        public function unregisterPagesBlocks($allowed_block_types, \WP_Block_Editor_Context $block_editor_context)
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
                'core/page-list',
            ];
            $allowed_block_types = array_diff($allowed_block_types, $blocks_to_remove);
            $allowed_block_types = array_values($allowed_block_types);

            return $allowed_block_types;
        }// unregisterPagesBlocks


        /**
         * Un-register Pages widgets.
         */
        public function unregisterPagesWidgets()
        {
            unregister_widget('WP_Widget_Pages');
        }// unregisterPagesWidgets


    }
}