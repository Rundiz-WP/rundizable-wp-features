<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisableMedia')) {
    /**
     * DisableMedia class.
     */
    class DisableMedia implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        /**
         * Class constructor.
         */
        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * Disable media actions (admin). Redirect the user to admin dashboard.
         * 
         * @param \WP_Screen $current_screen Current screen object.
         */
        public function disableMedia(\WP_Screen $current_screen)
        {
            if (
                is_object($current_screen) &&
                (
                    (
                        // in media page
                        property_exists($current_screen, 'post_type') &&
                        'attachment' === $current_screen->post_type &&
                        (
                            (
                                property_exists($current_screen, 'id') &&
                                'upload' === $current_screen->id
                            ) ||
                            (
                                property_exists($current_screen, 'action') &&
                                'add' === $current_screen->action
                            )
                        )
                    ) ||
                    (
                        // in upload page
                        property_exists($current_screen, 'id') &&
                        'media' === $current_screen->id &&
                        property_exists($current_screen, 'action') &&
                        'add' === $current_screen->action
                    ) ||
                    (
                        // in ajax upload
                        property_exists($current_screen, 'id') &&
                        (
                            'async-upload' === $current_screen->id ||
                            'options-media' === $current_screen->id
                        )
                    )
                )
            ) {
                wp_safe_redirect(admin_url());
                exit();
            }
        }// disableMedia


        /**
         * Disable media actions (front). Redirect the user to home page.
         */
        public function disableMediaFront()
        {
            if (
                !defined('DOING_CRON') &&
                is_attachment()
            ) {
                nocache_headers();
                wp_safe_redirect(home_url(), 301);
                exit();
            }
        }// disableMediaFront


        /**
         * Disable /media endpoints in REST API.
         *
         * @param array $endpoints The original endpoints.
         * @return array Return removed endpoints.
         */
        public function disableMediaInRestApi($endpoints)
        {
            if (is_array($endpoints)) {
                foreach ($endpoints as $key => $value) {
                    if (is_scalar($key) && preg_match('/^\/wp\/v2\/media/', $key)) {
                        unset($endpoints[$key]);
                    }
                }// endforeach;
                unset($key, $value);
            }

            return $endpoints;
        }// disableMediaInRestApi


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_media']) && strval($rundizable_wp_features_optname['disable_media']) === '1') {
                // admin bar
                add_action('wp_before_admin_bar_render', [$this, 'removeFromAdminBar']);
                // disable its functional
                add_action('current_screen', [$this, 'disableMedia']);
                add_action('admin_menu', [$this, 'removeMediaMenu']);
                // widgets and block
                add_action('widgets_init', [$this, 'unregisterMediaWidgets']);
                // REST API
                add_filter('rest_endpoints', [$this, 'disableMediaInRestApi']);
            }
        }// registerHooks


        /**
         * Remove media from admin bar.  
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
                'new-media',
            ];

            foreach ($menusToRemove as $item) {
                $wp_admin_bar->remove_menu($item);
            }
        }// removeFromAdminBar


        /**
         * Remove Media from admin menu
         */
        public function removeMediaMenu()
        {
            remove_menu_page('upload.php');
            remove_submenu_page('options-general.php', 'options-media.php');
        }// removeMediaMenu


        /**
         * Un-register Media widgets.
         */
        public function unregisterMediaWidgets()
        {
            unregister_widget('WP_Widget_Media_Audio');
            unregister_widget('WP_Widget_Media_Image');
            unregister_widget('WP_Widget_Media_Video');
            unregister_widget('WP_Widget_Media_Gallery');
        }// unregisterMediaWidgets


    }// DisableMedia
}
