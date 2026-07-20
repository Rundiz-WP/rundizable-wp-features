<?php
/**
 * Main app trait for common works.
 * 
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App;


if (!trait_exists('\\RundizableWpFeatures\\App\\AppTrait')) {
    /**
     * Main application trait.
     */
    trait AppTrait
    {


        /**
         * @var \RundizableWpFeatures\App\Libraries\Loader The loader class if it has been initiated. Make sure that this property must be set before use.
         */
        protected $Loader = null;


        /**
         * Main option name.
         * 
         * @var string Set main option name of this plugin. the name should be english, number, underscore, 
         *              or any characters that can be set to variable. 
         *              For example: `'rundizable_wp_features_optname'` will be set to `$rundizable_wp_features_optname`
         * @uses Call the trait method `getOptions();` before access `$rundizable_wp_features_optname` in global variable.
         */
        public $main_option_name = 'rundizable_wp_features_optname';


        /**
         * Get `Loader` object from `Loader` property.
         * 
         * This method is in main AppTrait.
         *
         * @return \RundizableWpFeatures\App\Libraries\Loader Return the `Loader` object.
         */
        protected function getLoader()
        {
            if (!$this->Loader instanceof \RundizableWpFeatures\App\Libraries\Loader) {
                $this->Loader = new \RundizableWpFeatures\App\Libraries\Loader();
            }
            return $this->Loader;
        }// getLoader


        /**
         * Get all options of this plugin from DB.
         * 
         * This method is in main AppTrait.
         * 
         * @return array Return associative array value of all options where the key is option name.
         */
        public function getOptions()
        {
            $option_name = $this->main_option_name;
            global ${$option_name};// phpcs:ignore PHPCompatibility.Variables.ForbiddenGlobalVariableVariable.NonBareVariableFound
            ${$option_name} = [];// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

            $get_option = get_option($option_name);
            if (false !== $get_option) {
                // if option has value.
                // `get_option()` already unserializes internally - no need to re-run `maybe_unserialize()`.
                if (is_string($get_option)) {
                    // if older version of this plugin may still use manual serialize/unserialize.
                    // @todo[rundiz] delete this `if` block on version 2.0+
                    $get_option = maybe_unserialize($get_option);
                    if (!is_array($get_option)) {
                        $get_option = [];
                    }
                }

                // process data before save with `save_callback` option. -----------------------------
                $config_values = $this->getLoader()->loadConfig();
                $settings_config_file = '';
                if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                    // if there is config value about config file.
                    $settings_config_file = $config_values['rundiz_settings_config_file'];
                }
                unset($config_values);

                $RundizSettings = new \RundizableWpFeatures\App\Libraries\RundizSettings();
                $RundizSettings->settings_config_file = $settings_config_file;
                $get_option = $RundizSettings->processDisplayCallback($get_option);
                unset($RundizSettings, $settings_config_file);
                // end process data before save with `save_callback` option. -------------------------

                ${$option_name} = (array) $get_option;// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
            }

            unset($get_option);
            return ${$option_name};// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        }// getOptions


        /**
         * Save the settings from settings page, using Rundiz settings.
         * 
         * This method is in main AppTrait.
         * 
         * @param array $data The associative array of submitted data in key => value
         * @return bool Return `true` if saved successfully. return `false` if not updated.
         */
        public function saveOptions(array $data)
        {
            $data = stripslashes_deep($data);

            // process data before save with `save_callback` option. -----------------------------
            $config_values = $this->getLoader()->loadConfig();
            $settings_config_file = '';
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                // if there is config value about config file.
                $settings_config_file = $config_values['rundiz_settings_config_file'];
            }
            unset($config_values);

            $RundizSettings = new \RundizableWpFeatures\App\Libraries\RundizSettings();
            $RundizSettings->settings_config_file = $settings_config_file;
            $data = $RundizSettings->processSaveCallback($data);
            unset($RundizSettings, $settings_config_file);
            // end process data before save with `save_callback` option. -------------------------

            // add manual update version into config value.
            if (!array_key_exists('rdsfw_manual_update_version', $data)) {
                $currentConfigValues = $this->getOptions();
                if (is_array($currentConfigValues) && array_key_exists('rdsfw_manual_update_version', $currentConfigValues)) {
                    $manual_update_version = $currentConfigValues['rdsfw_manual_update_version'];
                } else {
                    $manual_update_version = '';
                }
                unset($currentConfigValues);
                $data = array_merge($data, ['rdsfw_manual_update_version' => $manual_update_version]);
            }

            return update_option($this->main_option_name, $data, false);
        }// saveOptions


        /**
         * Set `Loader` object to `Loader` property.
         * 
         * This method is in main AppTrait.
         *
         * @param \RundizableWpFeatures\App\Libraries\Loader $Loader The `Loader` object.
         */
        public function setLoader(\RundizableWpFeatures\App\Libraries\Loader $Loader)
        {
            $this->Loader = $Loader;
        }// setLoader


    }// AppTrait
}
