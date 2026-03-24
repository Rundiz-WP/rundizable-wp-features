<?php
/**
 * Main app class. extend this class if you want to use any method of this class.
 * 
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App;


if (!class_exists('\\RundizableWpFeatures\\App\\App')) {
    /**
     * Main app class.
     */
    class App
    {


        /**
         * @var \RundizableWpFeatures\App\Libraries\Loader
         */
        public $Loader;


        /**
         * Run the WP plugin app.
         */
        public function run()
        {
            // Initialize the loader class.
            $this->Loader = new \RundizableWpFeatures\App\Libraries\Loader();
            $this->Loader->autoRegisterControllers();
        }// run


    }// App
}
