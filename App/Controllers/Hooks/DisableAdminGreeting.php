<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;



if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisableAdminGreeting')) {
    /**
     * Disable admin greeting class.
     * 
     * @since 0.2.7
     */
    class DisableAdminGreeting implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_admin_greeting']) && $rundizable_wp_features_optname['disable_admin_greeting'] == '1') {
                add_action('wp_before_admin_bar_render', [$this, 'removeHowdy']);
            }
        }// registerHooks


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
            $myAccountNode->title = preg_replace('/(\w+),(\s+)/u', '', $myAccountNode->title);
            $wp_admin_bar->remove_node('my-account');
            $wp_admin_bar->add_node($myAccountNode);
            unset($myAccountNode);
        }// removeHowdy


    }// DisableAdminGreeting
}// endif;
