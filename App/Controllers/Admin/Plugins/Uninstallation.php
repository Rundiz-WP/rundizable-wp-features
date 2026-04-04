<?php
/**
 * Uninstall or delete the plugin.
 * 
 * @package Rundizable-WP-Features
 * @since 1.0.3 Moved from part of App/Controllers/Admin/Activation.php
 */


namespace RundizableWpFeatures\App\Controllers\Admin\Plugins;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Admin\\Plugins\\Uninstallation')) {
    /**
     * Plugin uninstallation and site deletion (hard delete) hooks class.
     */
    class Uninstallation implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        /**
         * Get `main_option_name` property from trait which is non-static from any static method.
         * 
         * @return string Return main option name of this plugin. See `main_option_name` property for more info.
         */
        private static function getMainOptionName()
        {
            $class = new self();
            return $class->main_option_name;
        }// getMainOptionName


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // register uninstall hook. MUST be static method or function.
            register_uninstall_hook(RUNDIZABLEWPFEATURES_FILE, ['\\RundizableWpFeatures\\App\\Controllers\\Admin\\Plugins\\Uninstallation', 'uninstall']);
        }// registerHooks


        /**
         * Uninstall or delete the plugin.
         * 
         * @global \wpdb $wpdb
         */
        public static function uninstall()
        {
            // do something that will be happens on delete plugin.
            global $wpdb;
            $wpdb->show_errors();

            // delete options.
            if (is_multisite()) {
                // this is multi site, delete options in all sites.
                $blog_ids = get_sites(['fields' => 'ids', 'number' => 0]);
                $original_blog_id = get_current_blog_id();
                if ($blog_ids) {
                    foreach ($blog_ids as $blog_id) {
                        switch_to_blog($blog_id);
                        static::uninstallDeleteOption();
                    }
                }
                switch_to_blog($original_blog_id);
                unset($blog_id, $blog_ids, $original_blog_id);
            } else {
                // this is single site, delete options in single site.
                static::uninstallDeleteOption();
            }
        }// uninstall


        /**
         * Delete option on the switched to site.
         */
        private static function uninstallDeleteOption()
        {
            delete_option(static::getMainOptionName());
        }// uninstallDeleteOption


    }// Uninstallation
}
