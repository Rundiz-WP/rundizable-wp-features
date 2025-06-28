<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks\Admin\Users\Profile;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisableBio')) {
    /**
     * Disable Biographical Info
     * 
     * @since 0.2.7
     */
    class DisableBio implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * Enqueue script or style to hide related settings.
         * 
         * @param string $hook_suffix
         */
        public function enqueScriptToHideRelatedSettings($hook_suffix)
        {
            $userPages = [
                'profile.php',
                'user-new.php',
                'user-edit.php',
            ];

            if (in_array($hook_suffix, $userPages)) {
                wp_enqueue_style(
                    'rundizable-wp-feature-hide-user-profile-biographical-info', 
                    plugin_dir_url(RUNDIZABLEWPFEATURES_FILE) . 'assets/css/admin/users/profile/disable-biographical-info.css', 
                    [], 
                    RUNDIZABLEWPFEATURES_VERSION
                );
            }
        }// enqueScriptToHideRelatedSettings


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_users_profile_biographical_info']) && 
                $rundizable_wp_features_optname['disable_users_profile_biographical_info'] == '1'
            ) {
                // hide settings
                add_action('admin_enqueue_scripts', [$this, 'enqueScriptToHideRelatedSettings']);
            }
        }// registerHooks


    }// DisableBio
}// endif;
