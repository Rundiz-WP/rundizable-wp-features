<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;

if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisableFrontEnd')) {
    class DisableFrontEnd implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * Detect front-end and redirect to admin page.
         */
        public function detectFrontEnd()
        {
            if (
                !defined('DOING_CRON') &&
                (
                    !is_admin() || is_home() || is_front_page() || is_404()
                )
            ) {
                nocache_headers();
                wp_safe_redirect(admin_url(), 301);
                exit();
            }
        }// detectFrontEnd


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_frontend']) && $rundizable_wp_features_optname['disable_frontend'] == '0') {
                return ;
            }

            add_action('template_redirect', [$this, 'detectFrontEnd']);
            add_action('admin_menu', [$this, 'removeAppearanceMenu']);
            add_action('admin_menu', [$this, 'removeFrontRelatedMenu']);
        }// registerHooks


        /**
         * Remove Appearance and its sub menus from admin menu.
         */
        public function removeAppearanceMenu()
        {
            global $submenu;

            // remove all sub-menus of appearance menu.
            if (is_array($submenu) && array_key_exists('themes.php', $submenu)) {
                foreach ($submenu['themes.php'] as $pos => $items) {
                    if (is_array($items) && isset($items[2]) && is_scalar($items[2])) {
                        remove_submenu_page('themes.php', $items[2]);
                    }
                }// endforeach;
                unset($items, $pos);
            }

            // remove appearance menu itself.
            remove_menu_page('themes.php');
        }// removeAppearanceMenu


        /**
         * Remove front-end related menus.
         */
        public function removeFrontRelatedMenu()
        {
            remove_submenu_page('options-general.php', 'options-permalink.php');
            remove_submenu_page('options-general.php', 'privacy.php');
        }// removeFrontRelatedMenu


    }
}