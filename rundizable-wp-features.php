<?php
/**
 * Plugin Name: Rundizable WP Features
 * Plugin URI: http://rundiz.com
 * Description: Disable WordPress features.
 * Version: 0.2
 * Author: Vee Winch
 * Author URI: 
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: rundizable-wp-features
 * Domain Path: /languages/
 * 
 * @package Rundizable-WP-Features
 */


// define this plugin main file path.
if (!defined('RUNDIZABLEWPFEATURES_FILE')) {
    define('RUNDIZABLEWPFEATURES_FILE', __FILE__);
}


if (!defined('RUNDIZABLEWPFEATURES_VERSION')) {
    define('RUNDIZABLEWPFEATURES_VERSION', '0.2');
}


// include this plugin's autoload.
require __DIR__.'/autoload.php';


// initialize plugin app main class.
$this_plugin_app = new \RundizableWpFeatures\App\App();
$this_plugin_app->run();
unset($this_plugin_app);