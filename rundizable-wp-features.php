<?php
/**
 * Plugin Name: Rundizable WP Features
 * Plugin URI: https://rundiz.com/?p=319
 * Description: Disable WordPress features.
 * Version: 1.0.2
 * Requires at least: 4.6.0
 * Requires PHP: 5.5
 * Author: Vee Winch
 * Author URI: https://rundiz.com
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: rundizable-wp-features
 * Domain Path: /languages/
 * 
 * @package Rundizable-WP-Features
 */


if (!defined('ABSPATH')) {
    exit();
}


// define this plugin main file path.
if (!defined('RUNDIZABLEWPFEATURES_FILE')) {
    define('RUNDIZABLEWPFEATURES_FILE', __FILE__);
}


if (!defined('RUNDIZABLEWPFEATURES_VERSION')) {
    $rundizable_wp_features_pluginData = (function_exists('get_file_data') ? get_file_data(__FILE__, ['Version' => 'Version']) : null);
    $rundizable_wp_features_pluginVersion = (isset($rundizable_wp_features_pluginData['Version']) ? $rundizable_wp_features_pluginData['Version'] : date('Ym'));// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    unset($rundizable_wp_features_pluginData);
    define('RUNDIZABLEWPFEATURES_VERSION', $rundizable_wp_features_pluginVersion);
    unset($rundizable_wp_features_pluginVersion);
}


// include this plugin's autoload.
require __DIR__ . '/autoload.php';


// initialize plugin app main class.
$rundizable_wp_features_App = new \RundizableWpFeatures\App\App();
$rundizable_wp_features_App->run();
unset($rundizable_wp_features_App);
