<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisablePluginThemeFileEdit')) {
    /**
     * Disable plugin and theme file editor.
     * 
     * @since 0.2.7
     */
    class DisablePluginThemeFileEdit implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * Disable plugin and theme file editor.
         */
        public function disableFileEdit()
        {
            if (!defined('DISALLOW_FILE_EDIT')) {
                define('DISALLOW_FILE_EDIT', true);
                define('RUNDIZABLEWPFEATURES_CUSTOM_DISALLOW_FILE_EDIT', true);
            }
        }// disableFileEdit


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_plugintheme_file_editor']) && $rundizable_wp_features_optname['disable_plugintheme_file_editor'] == '1') {
                add_action('admin_init', [$this, 'disableFileEdit']);
            }
        }// registerHooks


    }// DisablePluginThemeFileEdit
}// endif;
