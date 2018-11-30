<?php
/**
 * Settings class is for add settings menu.
 * 
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App\Controllers\Admin;

if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Admin\\Settings')) {
    class Settings implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        /**
         * controller constructor
         */
        public function __construct() {
            // setup all options from setting config file.
            $this->setupAllOptions();
        }// __construct


        /**
         * an example of how to access settings variable and its values.
         * 
         * @global array $rundizable_wp_features_optname
         */
        public function pluginReadSettingsPage()
        {
            $this->getOptions();
            global $rundizable_wp_features_optname;

            $output['rundizable_wp_features_optname'] = $rundizable_wp_features_optname;

            $Loader = new \RundizableWpFeatures\App\Libraries\Loader();
            $Loader->loadView('admin/readsettings_v', $output);
            unset($Loader, $output);
        }// pluginReadSettingsPage


        /**
         * setup settings menu to go to settings page.
         */
        public function pluginSettingsMenu()
        {
            $hook_suffix = add_options_page(__('Rundizable WP Features', 'rundizable-wp-features'), __('Rundizable WP Features', 'rundizable-wp-features'), 'manage_options', 'rundizable-wp-features-settings', [$this, 'pluginSettingsPage']);
            add_action('load-' . $hook_suffix, [$this, 'registerScripts']);
            unset($hook_suffix);

            //add_options_page(__('Rundizable WP Features read settings value', 'rundizable-wp-features'), __('Rundizable WP Features read settings', 'rundizable-wp-features'), 'manage_options', 'rundizable-wp-features-read-settings', [$this, 'pluginReadSettingsPage']);
        }// pluginSettingsMenu


        /**
         * display plugin settings page.
         */
        public function pluginSettingsPage()
        {
            // check permission.
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have permission to access this page.'));
            }

            // load config values to get settings config file.
            $loader = new \RundizableWpFeatures\App\Libraries\Loader();
            $config_values = $loader->loadConfig();
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                $settings_config_file = $config_values['rundiz_settings_config_file'];
            } else {
                echo 'Settings configuration file was not set.';
                die('Settings configuration file was not set.');
                exit;
            }
            unset($config_values, $loader);

            $RundizSettings = new \RundizableWpFeatures\App\Libraries\RundizSettings();
            $RundizSettings->settings_config_file = $settings_config_file;

            $options_values = $this->getOptions();

            // if form submitted
            if (isset($_POST) && !empty($_POST)) {
                if (!wp_verify_nonce((isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : ''))) {
                    wp_nonce_ays('-1');
                }

                // populate form field values.
                $options_values = $RundizSettings->getSubmittedData();

                // you may validate form here first.
                // then save data.
                $result = $this->saveOptions($options_values);

                if ($result === true) {
                    $output['form_result_class'] = 'notice-success';
                    $output['form_result_msg'] = __('Settings saved.');
                } else {
                    $output['form_result_class'] = 'notice-success';
                    $output['form_result_msg'] =  __('Settings saved.');
                }
            }// endif $_POST

            $output['settings_page'] = $RundizSettings->getSettingsPage($options_values);
            unset($RundizSettings, $options_values);

            $Loader = new \RundizableWpFeatures\App\Libraries\Loader();
            $Loader->loadView('admin/settings_v', $output);
            unset($Loader, $output);
        }// pluginSettingsPage


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            if (is_admin()) {
                add_action('admin_menu', [&$this, 'pluginSettingsMenu']);
            }
        }// registerHooks


        /**
         * enqueue scripts and styles here.
         */
        public function registerScripts()
        {
            wp_enqueue_style('font-awesome5', plugin_dir_url(RUNDIZABLEWPFEATURES_FILE).'assets/css/fa-svg-with-js.css', [], '5.0.13');
            wp_enqueue_style('rd-settings-tabs-css', plugin_dir_url(RUNDIZABLEWPFEATURES_FILE).'assets/css/rd-settings-tabs.css', [], RUNDIZABLEWPFEATURES_VERSION);
            wp_enqueue_script('rd-settings-tabs-js', plugin_dir_url(RUNDIZABLEWPFEATURES_FILE).'assets/js/rd-settings-tabs.js', ['jquery'], RUNDIZABLEWPFEATURES_VERSION, true);
            wp_enqueue_script('font-awesome5', plugin_dir_url(RUNDIZABLEWPFEATURES_FILE).'assets/js/fontawesome-all.min.js', [], '5.0.13', true);
        }// registerScripts


    }
}