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
        public function disablePages($current_screen)
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
                    if (is_scalar($key) && strpos($key, '/pages') !== false) {
                        unset($endpoints[$key]);
                    }
                }// endforeach;
                unset($key, $value);
            }

            return $endpoints;
        }// disablePagesInRestApi


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_pages']) && $rundizable_wp_features_optname['disable_pages'] == '1') {
                add_action('current_screen', [$this, 'disablePages']);
                add_action('admin_menu', [$this, 'removePagesMenu']);
                add_action('widgets_init', [$this, 'unregisterPagesWidgets']);
                add_action('admin_init', [$this, 'updateReadSettings']);
                add_filter('rest_endpoints', [$this, 'disablePagesInRestApi']);
            }

            if (isset($rundizable_wp_features_optname['disable_pages_front']) && $rundizable_wp_features_optname['disable_pages_front'] == '1') {
                add_action('template_redirect', [$this, 'disablePagesFront']);
            }
        }// registerHooks


        /**
         * Remove Pages from admin menu.
         */
        public function removePagesMenu()
        {
            remove_menu_page('edit.php?post_type=page');
        }// removePagesMenu


        /**
         * Un-register Pages widgets.
         */
        public function unregisterPagesWidgets()
        {
            unregister_widget('WP_Widget_Pages');
        }// unregisterPagesWidgets


        public function updateReadSettings()
        {
            // don't do anything here, let the user select page for home themselve.
        }// updateReadSettings


    }
}