<?php
/**
 * Deactivate the plugin action.
 * 
 * @package Rundizable-WP-Features
 * @since 1.0.3 Moved from part of App/Controllers/Admin/Activation.php
 */


namespace RundizableWpFeatures\App\Controllers\Admin\Plugins;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Admin\\Plugins\\Deactivation')) {
    /**
     * Plugin deactivation hook class.
     */
    class Deactivation implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        /**
         * Deactivate the plugin.
         */
        public function deactivate()
        {
            // Do something that will be happens on deactivate plugin.
        }// deactivate


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // register deactivate hook
            register_deactivation_hook(RUNDIZABLEWPFEATURES_FILE, [$this, 'deactivate']);
        }// registerHooks


    }// Deactivation
}
