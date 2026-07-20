<?php
/**
 * Main app class. Extend this class if you want to use any method of this class.
 *
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\RundizableWpFeatures\\App\\App')) {
    /**
     * Plugin application main entry class.
     */
    class App
    {


        use AppTrait;


        /**
         * Run the WP plugin app.
         */
        public function run()
        {
            // Any method that must be called before auto register controllers must be manually write it down here, below this line.
            $StylesAndScripts = new \RundizableWpFeatures\App\Libraries\StylesAndScripts();
            $StylesAndScripts->manualRegisterHooks();
            unset($StylesAndScripts);

            // Initialize the loader class.
            $this->Loader = new \RundizableWpFeatures\App\Libraries\Loader();
            $this->Loader->autoRegisterControllers();
        }// run


    }// App
}
