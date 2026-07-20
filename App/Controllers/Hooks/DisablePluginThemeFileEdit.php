<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisablePluginThemeFileEdit')) {
    /**
     * Disable plugin and theme file editor.
     * 
     * @since 0.2.7
     */
    class DisablePluginThemeFileEdit extends \RundizableWpFeatures\App\Controllers\Hooks\BasedHooks
    {


        /**
         * Disable plugin and theme file editor.
         */
        public function disableFileEdit()
        {
            if (!defined('DISALLOW_FILE_EDIT')) {
                define('DISALLOW_FILE_EDIT', true);// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
                define('RUNDIZABLEWPFEATURES_CUSTOM_DISALLOW_FILE_EDIT', true);
            }
        }// disableFileEdit


        /**
         * Register hooks per this class only.
         * 
         * @since 1.0.7 Renamed from `registerHooks()`.
         */
        public function perClassRegisterHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_plugintheme_file_editor']) && strval($rundizable_wp_features_optname['disable_plugintheme_file_editor']) === '1') {
                add_action('admin_init', [$this, 'disableFileEdit']);
            }
        }// perClassRegisterHooks


    }// DisablePluginThemeFileEdit
}// endif;
