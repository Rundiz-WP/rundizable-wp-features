<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks\Admin\Users\Profile;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\Admin\\Users\\Profile\\DisableAdminColorScheme')) {
    /**
     * Disable Admin Color Scheme in user profile page.
     */
    class DisableAdminColorScheme implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * Use `remove_action()` to disable Admin Color Scheme hook name `admin_color_scheme_picker`.  
         * Also reset if user had setting about color scheme to something else that is not matched default.
         */
        public function disableAdminColorSchemeAction()
        {
            remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');

            // check if user had setting and its value is not match default then set to default.
            $userId = get_current_user_id();
            $colorOptionName = 'admin_color';
            $defaultColor = 'fresh';
            $adminColorOption = get_user_option($colorOptionName, $userId);
            $adminColorMeta = get_user_meta($userId, $colorOptionName, true);

            if ($defaultColor !== $adminColorOption || $defaultColor !== $adminColorMeta) {
                // if ANY of option or meta does not matched default value.
                // update this user's option to default.
                if (is_multisite()) {
                    update_user_option($userId, $colorOptionName, $defaultColor);
                } else {
                    update_user_meta($userId, $colorOptionName, $defaultColor);
                }
            }
            unset($colorOptionName, $defaultColor);
            unset($adminColorMeta, $adminColorOption, $userId);
        }// disableAdminColorSchemeAction


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
                    'rundizable-wp-feature-hide-user-profile-admin-color-scheme', 
                    plugin_dir_url(RUNDIZABLEWPFEATURES_FILE) . 'assets/css/admin/users/profile/disable-admin-color-scheme.css', 
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
            if (isset($rundizable_wp_features_optname['disable_users_profile_admin_color_scheme']) && 
                $rundizable_wp_features_optname['disable_users_profile_admin_color_scheme'] == '1'
            ) {
                // hide settings
                add_action('admin_enqueue_scripts', [$this, 'enqueScriptToHideRelatedSettings']);
                add_action('admin_head', [$this, 'disableAdminColorSchemeAction']);
            }
        }// registerHooks


    }// DisableAdminColorScheme
}// endif
