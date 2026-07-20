<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App\Controllers\Hooks\Admin\Users\Profile;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisableBio')) {
    /**
     * Disable Biographical Info
     * 
     * @since 0.2.7
     */
    class DisableBio extends \RundizableWpFeatures\App\Controllers\Hooks\BasedHooks
    {


        /**
         * Enqueue script or style to hide related settings.
         * 
         * @param string $hook_suffix Hook suffix.
         */
        public function enqueScriptToHideRelatedSettings($hook_suffix)
        {
            $userPages = [
                'profile.php',
                'user-new.php',
                'user-edit.php',
            ];

            if (in_array($hook_suffix, $userPages, true)) {
                wp_enqueue_style(
                    'rundizable-wp-feature-hide-user-profile-biographical-info', 
                    plugin_dir_url(RUNDIZABLEWPFEATURES_FILE) . 'assets/css/Admin/users/profile/disable-biographical-info.css', 
                    [], 
                    RUNDIZABLEWPFEATURES_VERSION
                );
            }
        }// enqueScriptToHideRelatedSettings


        /**
         * Register hooks per this class only.
         * 
         * @since 1.0.7 Renamed from `registerHooks()`.
         */
        public function perClassRegisterHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_users_profile_biographical_info']) && 
                strval($rundizable_wp_features_optname['disable_users_profile_biographical_info']) === '1'
            ) {
                // hide settings
                add_action('admin_enqueue_scripts', [$this, 'enqueScriptToHideRelatedSettings']);
            }
        }// perClassRegisterHooks


    }// DisableBio
}// endif;
