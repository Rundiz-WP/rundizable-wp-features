<?php
/**
 * Based hooks class.
 * 
 * @since 1.0.7
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\RundizableWpFeatures\App\Controllers\Hooks\\BasedHooks')) {
    /**
     * Based hooks controller.
     * 
     * @since 1.0.7
     */
    abstract class BasedHooks implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        /**
         * Get plugin options by using hook to prevent error load translation function too early.
         * 
         * @since 1.0.7
         */
        public function basedGetPluginSettings()
        {
            $this->getOptions();
            
            $this->perClassRegisterHooks();
        }// basedGetPluginSettings


        /**
         * {@inheritDoc}
         * 
         * @since 1.0.7
         */
        public function registerHooks()
        {
            add_action('init', [$this, 'basedGetPluginSettings']);
        }// registerHooks


    }// BasedHooks
}
