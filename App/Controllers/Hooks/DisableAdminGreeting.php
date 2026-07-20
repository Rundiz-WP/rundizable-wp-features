<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;



if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisableAdminGreeting')) {
    /**
     * Disable admin greeting class.
     * 
     * @since 0.2.7
     */
    class DisableAdminGreeting extends \RundizableWpFeatures\App\Controllers\Hooks\BasedHooks
    {


        /**
         * Register hooks per this class only.
         * 
         * @since 1.0.7 Renamed from `registerHooks()`.
         */
        public function perClassRegisterHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_admin_greeting']) && strval($rundizable_wp_features_optname['disable_admin_greeting']) === '1') {
                add_action('wp_before_admin_bar_render', [$this, 'removeHowdy']);
            }
        }// perClassRegisterHooks


        /**
         * Remove admin greeting.
         * 
         * @global \WP_Admin_Bar $wp_admin_bar
         */
        public function removeHowdy()
        {
            /* @var $wp_admin_bar \WP_Admin_Bar */
            global $wp_admin_bar;

            $myAccountNode = $wp_admin_bar->get_node('my-account');
            $myAccountNode->title = preg_replace('/^.*?<span class="display-name"/s', '<span class="display-name"', $myAccountNode->title);
            $wp_admin_bar->remove_node('my-account');
            $wp_admin_bar->add_node($myAccountNode);
            unset($myAccountNode);
        }// removeHowdy


    }// DisableAdminGreeting
}// endif;
