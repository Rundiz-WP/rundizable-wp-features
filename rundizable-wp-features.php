<?php
/**
 * Plugin Name: Rundizable WP Features
 * Plugin URI: https://rundiz.com/?p=319
 * Description: Disable WordPress features.
 * Version: 0.2.3
 * Requires at least: 4.6.0
 * Requires PHP: 5.5
 * Author: Vee Winch
 * Author URI: http://rundiz.com
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
    $pluginData = (function_exists('get_file_data') ? get_file_data(__FILE__, ['Version' => 'Version']) : null);
    $pluginVersion = (isset($pluginData['Version']) ? $pluginData['Version'] : date('Ym'));
    unset($pluginData);
    define('RUNDIZABLEWPFEATURES_VERSION', $pluginVersion);
    unset($pluginVersion);
}


// include this plugin's autoload.
require __DIR__.'/autoload.php';


// initialize plugin app main class.
$this_plugin_app = new \RundizableWpFeatures\App\App();
$this_plugin_app->run();
unset($this_plugin_app);