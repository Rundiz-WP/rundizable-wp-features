<?php
/**
 * The manual update for running new version of code.
 * 
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App\Update\Manual;


if (!interface_exists('\\RundizableWpFeatures\\App\\Update\\Manual\\ManualUpdateInterface')) {
    /**
     * Manual update interface.
     */
    interface ManualUpdateInterface
    {


        /**
         * Run the manual update code.
         */
        public function run();


    }// ManualUpdateInterface
}
