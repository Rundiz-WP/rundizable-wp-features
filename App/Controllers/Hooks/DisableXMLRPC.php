<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisableXMLRPC')) {
    /**
     * Disable XML-RPC
     * 
     * @since 0.2.7
     */
    class DisableXMLRPC extends \RundizableWpFeatures\App\Controllers\Hooks\BasedHooks
    {


        /**
         * Disable XML-RPC headers.
         * 
         * @param array $headers Response headers.
         * @param \WP $wp The `\WP` object.
         * @return array
         */
        public function disableXmlrpcHeaders($headers, \WP $wp)
        {
            if (isset($headers['X-Pingback'])) {
                unset($headers['X-Pingback']);
            }
            return $headers;
        }// disableXmlrpcHeaders


        /**
         * Disable XML-RPC methods.
         * 
         * @link https://wordpress.stackexchange.com/a/300198/41315 Reference.
         * @param array $methods HTTP Methods
         * @return array
         */
        public function disableXmlrpcMethods(array $methods)
        {
            $methods = [];
            return $methods;
        }// disableXmlrpcMethods


        /**
         * Register hooks per this class only.
         * 
         * @since 1.0.7 Renamed from `registerHooks()`.
         */
        public function perClassRegisterHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_xmlrpc']) && strval($rundizable_wp_features_optname['disable_xmlrpc']) === '1') {
                add_filter('xmlrpc_enabled', '__return_false');
                add_filter('xmlrpc_methods', [$this, 'disableXmlrpcMethods']);
                add_filter('wp_headers', [$this, 'disableXmlrpcHeaders'], 10, 2);
                add_filter('bloginfo_url', [$this, 'removePingbackURLFromBloginfo'], 10, 2);
                add_filter('pings_open', '__return_false', 10, 2);
                add_action('wp', [$this, 'removeLinkEditURI']);
            }
        }// perClassRegisterHooks


        /**
         * Remove link to EditURI. (`&lt;link rel="EditURI"&gt;`).
         */
        public function removeLinkEditURI()
        {
            remove_action('wp_head', 'rsd_link');
        }// removeLinkEditURI


        /**
         * Remove pingback URL from blog info. This will only remove pingback URL in the link tag.
         * 
         * @link https://developer.wordpress.org/reference/hooks/bloginfo_url/ Reference.
         * @param string $output The URL returned by bloginfo() .
         * @param string $show Type of information requested.
         * @return string
         */
        public function removePingbackURLFromBloginfo($output, $show)
        {
            return ('pingback_url' === $show ? null : $output);
        }// removePingbackURLFromBloginfo


    }// DisableXMLRPC
}// endif;
