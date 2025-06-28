<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks\Admin\Users\Profile;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\Admin\\Users\\Profile\\DisableWebsite')) {
    class DisableWebsite implements \RundizableWpFeatures\App\Controllers\ControllerInterface
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
                    'rundizable-wp-feature-hide-user-profile-website', 
                    plugin_dir_url(RUNDIZABLEWPFEATURES_FILE) . 'assets/css/admin/users/profile/disable-website.css', 
                    [], 
                    RUNDIZABLEWPFEATURES_VERSION
                );
                // The add new user page doesn't have CSS class on the `<tr>` like edit user pages had.
                // So, this page is required JS to work.
                wp_enqueue_script(
                    'rundizable-wp-features-hide-user-profile-website-js',
                    plugin_dir_url(RUNDIZABLEWPFEATURES_FILE) . 'assets/js/admin/users/profile/disable-website.js',
                    [],
                    RUNDIZABLEWPFEATURES_VERSION,
                    true// use `true` for WP older than 6.3.0
                );
            }
        }// enqueScriptToHideRelatedSettings


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_users_profile_website']) && 
                $rundizable_wp_features_optname['disable_users_profile_website'] == '1'
            ) {
                // hide settings
                add_action('admin_enqueue_scripts', [$this, 'enqueScriptToHideRelatedSettings']);
            }
        }// registerHooks


    }// DisableWebsite
}// endif;
