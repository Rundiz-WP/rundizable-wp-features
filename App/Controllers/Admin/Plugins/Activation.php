<?php
/**
 * Activate the plugin action.
 *
 * @package Rundizable-WP-Features
 * @since 1.0.3 Moved from App/Controllers/Admin/Activation.php
 */


namespace RundizableWpFeatures\App\Controllers\Admin\Plugins;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Admin\\Plugins\\Activation')) {
    /**
     * Plugin activation and new site activation hooks class.
     */
    class Activation implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;

        /**
         * All available options.
         * 
         * These options will be accessible via main option name variable.  
         * For example: options name `'the_name'` can call from `$rundizable_wp_features_optname['the_name'];`.  
         * (`$rundizable_wp_features_optname` will be set via the property's value in `AppTrait->main_option_name`.)  
         * If you want to access this property, please call to `setupAllOptions()` method first.
         * 
         * @since 2015-09-05 First was set in the `AppTrait`.
         * @since 2026-07-20 Moved from `AppTrait`.
         * @var array Set all options available for this plugin. it must be 2D array (`key => default value, key2 => default value, ...`)
         */
        public $all_options = [];


        /**
         * Activate the plugin by admin on WP plugin page.
         *
         * @link https://developer.wordpress.org/reference/functions/register_activation_hook/ The function `register_activation_hook()` reference.
         * @link https://developer.wordpress.org/reference/hooks/activate_plugin/ The reference about what will be pass to callback of function `register_activation_hook()`.
         * @global \wpdb $wpdb WordPress DB class.
         * @param bool $network_wide Whether to enable the plugin for all sites in the network or just the current site. Multisite only. Default false.
         * @throws \Exception Throw the exception if failed to detect current version of PHP.
         */
        public function activate($network_wide)
        {
            // Do something that will happens on activate plugin.
            // @todo [rd-settings-fw] In your project, you may need to verify PHP version and/or WordPress version before did activate.
            $wordpress_required_version = '4.6.0';
            $phpversion_required = '5.5';
            if (function_exists('phpversion')) {
                $phpversion = phpversion();
            }
            if (!isset($phpversion) || (isset($phpversion) && false === $phpversion)) {
                if (defined('PHP_VERSION')) {
                    $phpversion = PHP_VERSION;
                } else {
                    // if there is no defined constant `PHP_VERSION`.
                    // @link https://www.php.net/ChangeLog-4.php Reference.
                    throw new \Exception('You are using ancient version of PHP. The constant `PHP_VERSION` is available since PHP 4.0.');
                }
            }
            if (version_compare($phpversion, $phpversion_required, '<')) {
                wp_die(
                    esc_html(
                        sprintf(
                            /* translators: %1$s current PHP version. */
                            __('You are using PHP %1$s which does not meet minimum requirement. Please consider upgrade PHP version or contact plugin author for this help.', 'rundizable-wp-features'),
                            $phpversion
                        )
                    )
                    . '<br><br>'
                    . esc_html(
                        sprintf(
                            /* translators: %1$s minimum PHP version required. */
                            __('Minimum PHP requirement: %1$s.', 'rundizable-wp-features'),
                            $phpversion_required
                        )
                    ), 
                    esc_html__('Minimum requirement of PHP version does not meet.', 'rundizable-wp-features')
                );
                exit(1);
            }// endif;
            if (version_compare(get_bloginfo('version'), $wordpress_required_version, '<')) {
                wp_die(
                    esc_html(
                        sprintf(
                            // translators: %1$s Current WordPress version, %2$s Required WordPress version.
                            __('Your WordPress version does not meet the requirement. (%1$s < %2$s).', 'rundizable-wp-features'), 
                            get_bloginfo('version'),
                            $wordpress_required_version
                        )
                    ),
                    esc_html__('Minimum requirement of WordPress version does not meet.', 'rundizable-wp-features')
                );
                exit(1);
            }// endif;
            unset($phpversion, $phpversion_required, $wordpress_required_version);

            // Get `$wpdb` global var.
            global $wpdb;
            $wpdb->show_errors();

            // Add option to site or multisite -----------------------------
            if (is_multisite()) {
                // This site is multisite. Add/update options, create/alter tables on all sites.
                $blog_ids = get_sites(['fields' => 'ids', 'number' => 0]);
                $original_blog_id = get_current_blog_id();
                if ($blog_ids) {
                    foreach ($blog_ids as $blog_id) {
                        switch_to_blog($blog_id);
                        $this->activateAddUpdateOption();
                    }
                }
                switch_to_blog($original_blog_id);
                unset($blog_id, $blog_ids, $original_blog_id);
            } else {
                // This site is single site. activate on single site.
                $this->activateAddUpdateOption();
            }
        }// activate


        /**
         * Check if the options was added before or not, if not then add the options otherwise update them.
         */
        private function activateAddUpdateOption()
        {
            // check current option exists or not.
            $current_options = get_option($this->main_option_name);

            if (false === $current_options) {
                // if this is newly activate. it is never activated before, add the options.
                $this->saveOptions($this->getAllOptions());
            }// endif;

            unset($current_options);
        }// activateAddUpdateOption


        /**
         * Get value of `all_options` property. The value of this property is from settings config file, not from DB.
         * 
         * Also setup if it was not set before.
         * 
         * This method visibility is `protected` to let tests class extend and use it.
         * 
         * @since 2026-07-20
         * @return array Return array value of `all_options` property.
         */
        protected function getAllOptions()
        {
            if (!is_array($this->all_options) || empty($this->all_options)) {
                $this->setupAllOptions();
            }

            return $this->all_options;
        }// getAllOptions


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // register activate hook
            register_activation_hook(RUNDIZABLEWPFEATURES_FILE, [$this, 'activate']);
        }// registerHooks


        /**
         * Setup all options from settings config file.
         * 
         * This will be set all config settings into `all_options` property.
         * You have to call this method if you want to call to `all_options` property.
         * 
         * This method will not load saved settings data from DB. The value in settings fields are all default value.
         * 
         * This method was called from `getAllOptions()`.
         * 
         * @since 2015-09-05 First was set in the `AppTrait`.
         * @since 2026-07-20 Moved from `AppTrait`.
         */
        private function setupAllOptions()
        {
            // load config values to get settings config file.
            $config_values = $this->getLoader()->loadConfig();
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                // if there is config value about config file.
                $settings_config_file = $config_values['rundiz_settings_config_file'];
            } else {
                // if there is no config value about config file.
                wp_die(
                    esc_html__('Settings configuration file was not set.', 'rundizable-wp-features')
                );
                exit(1);
            }
            unset($config_values);

            $RundizSettings = new \RundizableWpFeatures\App\Libraries\RundizSettings();
            $RundizSettings->settings_config_file = $settings_config_file;
            $this->all_options = $RundizSettings->getSettingsFieldsId();
            unset($RundizSettings, $settings_config_file);

            // add db version into config value.
            if (is_array($this->all_options)) {
                if (!array_key_exists('rdsfw_manual_update_version', $this->all_options)) {
                    $this->all_options = array_merge($this->all_options, ['rdsfw_manual_update_version' => '']);
                }
            }
        }// setupAllOptions


    }// Activation
}
