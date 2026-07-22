<?php
/**
 * Add settings sub menu and page into the Settings menu.
 * 
 * Original source last update: 2026-04-11
 * 
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App\Controllers\Admin;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Admin\\Settings')) {
    /**
     * Admin settings page.
     */
    class Settings implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        /**
         * @var string Settings menu slug. This class constant visibility must be public.
         */
        const MENU_SLUG = 'rundizable-wp-features-settings';


        /**
         * @var string The current admin page.
         */
        private $hookSuffix = '';


        /**
         * Allow code/WordPress to call hook `admin_enqueue_scripts` 
         * then `wp_register_script()`, `wp_localize_script()`, `wp_enqueue_script()` functions will be working fine later.
         * 
         * @link https://wordpress.stackexchange.com/a/76420/41315 Original source code.
         * @since 2025-10-14
         */
        public function callEnqueueHook()
        {
            add_action('admin_enqueue_scripts', [$this, 'registerScripts']);
        }// callEnqueueHook


        /**
         * An example of how to access settings variable and its values.
         * 
         * @global array $rundizable_wp_features_optname
         */
        public function pluginReadSettingsPage()
        {
            $this->getOptions();
            global $rundizable_wp_features_optname;

            $output = [];
            $output['rundizable_wp_features_optname'] = $rundizable_wp_features_optname;

            $this->getLoader()->loadView('Admin/readsettings_v', $output);
            unset($output);
        }// pluginReadSettingsPage


        /**
         * The plugin settings sub menu to go to settings page.
         */
        public function pluginSettingsMenu()
        {
            $hook_suffix = add_options_page(__('Rundizable WP Features', 'rundizable-wp-features'), __('Rundizable WP Features', 'rundizable-wp-features'), 'manage_options', static::MENU_SLUG, [$this, 'pluginSettingsPage']);
            if (is_string($hook_suffix)) {
                $this->hookSuffix = $hook_suffix;
                add_action('load-' . $hook_suffix, [$this, 'callEnqueueHook']);
            }
            unset($hook_suffix);

            //add_options_page(__('Rundizable WP Features (read settings)', 'rundizable-wp-features'), __('Rundizable WP Features (read settings)', 'rundizable-wp-features'), 'manage_options', 'rundizable-wp-features-read-settings', [$this, 'pluginReadSettingsPage']);
        }// pluginSettingsMenu


        /**
         * Display plugin settings page.
         */
        public function pluginSettingsPage()
        {
            // check permission.
            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('You do not have permission to access this page.', 'rundizable-wp-features'));
            }

            if (get_transient('rundizable_wp_features_updated')) {
                if (current_user_can('update_plugins')) {
                    wp_die(
                        sprintf(
                            // translators: %1$s Open link, %2$s Close link.
                            esc_html__('The manual update is required, please %1$supdate first%2$s.', 'rundizable-wp-features'),
                            '<a href="' . esc_url(network_admin_url('index.php?page=' . rawurlencode(Plugins\Upgrader::MENU_SLUG))) . '">', 
                            '</a>'
                        )
                    );
                } else {
                    wp_die(
                        esc_html__('The manual update is required, please tell administrator to update first.', 'rundizable-wp-features')
                    );
                }
            }

            // load config values to get settings config file.
            $config_values = $this->getLoader()->loadConfig();
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                $settings_config_file = $config_values['rundiz_settings_config_file'];
            } else {
                wp_die(esc_html__('Settings configuration file was not set.', 'rundizable-wp-features'));
                exit(1);
            }
            unset($config_values);

            $RundizSettings = new \RundizableWpFeatures\App\Libraries\RundizSettings();
            $RundizSettings->settings_config_file = $settings_config_file;

            $options_values = $this->getOptions();
            $output = [];

            // if form submitted
            if (isset($_POST) && !empty($_POST)) {
                $wpnonce = '';
                if (isset($_POST['_wpnonce'])) {
                    $wpnonce = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
                }

                if (!wp_verify_nonce($wpnonce)) {
                    wp_nonce_ays('-1');
                }
                unset($wpnonce);

                // populate form field values.
                $options_values = $RundizSettings->getSubmittedData();

                // you may validate form here first.
                // then save data.
                $output['save_result'] = $this->saveOptions($options_values);

                $output['form_result_class'] = 'notice-success';
                $output['form_result_msg'] = __('Settings saved.', 'rundizable-wp-features');
            }// endif $_POST

            $output['settings_page'] = $RundizSettings->getSettingsPage($options_values);
            unset($RundizSettings, $options_values);

            $this->getLoader()->loadView('Admin/settings_v', $output);
            unset($output);
        }// pluginSettingsPage


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            add_action('admin_menu', [$this, 'pluginSettingsMenu']);
        }// registerHooks


        /**
         * Enqueue scripts and styles here.
         * 
         * @param string $hook_suffix The current admin page.
         */
        public function registerScripts($hook_suffix = '')
        {
            if ($hook_suffix !== $this->hookSuffix) {
                return;
            }

            wp_enqueue_style('rundizable-wp-features-font-awesome5');

            wp_enqueue_style('rundizable-wp-features-rd-settings-tabs-css');
            wp_enqueue_script('rundizable-wp-features-rd-settings-tabs-js');
        }// registerScripts


    }// Settings
}
