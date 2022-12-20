<?php
/**
 * Main app class. extend this class if you want to use any method of this class.
 * 
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App;

use RundizableWpFeatures\App\Controllers as Controllers;

if (!class_exists('\\RundizableWpFeatures\\App\\App')) {
    class App
    {


        /**
         * @var \RundizableWpFeatures\App\Libraries\Loader
         */
        public $Loader;


        /**
         * load text domain. (language files)
         */
        public function loadLanguage()
        {
            load_plugin_textdomain('rundizable-wp-features', false, dirname(plugin_basename(RUNDIZABLEWPFEATURES_FILE)) . '/languages/');
        }// loadLanguage


        /**
         * run the wp plugin app.
         */
        public function run()
        {
            add_action('plugins_loaded', function() {
                // @link https://codex.wordpress.org/Function_Reference/load_plugin_textdomain Reference.
                // load language of this plugin.
                $this->loadLanguage();
            });

            // Initialize the loader class.
            $this->Loader = new \RundizableWpFeatures\App\Libraries\Loader();
            $this->Loader->autoRegisterControllers();

            // The rest of controllers that is not able to register via loader's auto register.
            // They must be manually write it down here, below this line.
            // For example:
            // $SomeController = new \RundizableWpFeatures\App\Controllers\SomeController();
            // $SomeController->runItHere();
            // unset($SomeController);// for clean up memory.
            // ------------------------------------------------------------------------------------
        }// run


    }
}