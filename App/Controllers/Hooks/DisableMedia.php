<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;

if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisableMedia')) {
    class DisableMedia implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * Disable media actions (admin). Redirect the user to admin dashboard.
         * 
         * @param \WP_Screen $current_screen
         */
        public function disableMedia($current_screen)
        {
            if (
                is_object($current_screen) &&
                (
                    (
                        // in media page
                        property_exists($current_screen, 'post_type') &&
                        $current_screen->post_type === 'attachment' &&
                        (
                            (
                                property_exists($current_screen, 'id') &&
                                $current_screen->id === 'upload'
                            ) ||
                            (
                                property_exists($current_screen, 'action') &&
                                $current_screen->action === 'add'
                            )
                        )
                    ) ||
                    (
                        // in upload page
                        property_exists($current_screen, 'id') &&
                        $current_screen->id === 'media' &&
                        property_exists($current_screen, 'action') &&
                        $current_screen->action === 'add'
                    ) ||
                    (
                        // in ajax upload
                        property_exists($current_screen, 'id') &&
                        $current_screen->id === 'async-upload'
                    ) ||
                    (
                        // in settings > media
                        property_exists($current_screen, 'id') &&
                        $current_screen->id === 'options-media'
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
                    if (is_scalar($key) && strpos($key, '/media') !== false) {
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
            if (isset($rundizable_wp_features_optname['disable_media']) && $rundizable_wp_features_optname['disable_media'] == '1') {
                add_action('current_screen', [$this, 'disableMedia']);
                add_action('admin_menu', [$this, 'removeMediaMenu']);
                add_action('widgets_init', [$this, 'unregisterMediaWidgets']);
                add_filter('rest_endpoints', [$this, 'disableMediaInRestApi']);
            }

            if (isset($rundizable_wp_features_optname['disable_media_front']) && $rundizable_wp_features_optname['disable_media_front'] == '1') {
                add_action('template_redirect', [$this, 'disableMediaFront']);
            }
        }// registerHooks


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


    }
}