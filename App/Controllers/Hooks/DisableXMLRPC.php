<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisableXMLRPC')) {
    /**
     * Disable XML-RPC
     * 
     * @since 0.2.7
     */
    class DisableXMLRPC implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * Disable XML-RPC headers.
         * 
         * @param array $headers
         * @param \WP $wp
         * @return array
         */
        public function disableXmlrpcHeaders($headers, \WP $wp)
        {
            if(isset($headers['X-Pingback'])){
                unset($headers['X-Pingback']);
            }
            return $headers;
        }// disableXmlrpcHeaders


        /**
         * Disable XML-RPC methods.
         * 
         * @link https://wordpress.stackexchange.com/a/300198/41315 Reference.
         * @param array $methods
         * @return array
         */
        public function disableXmlrpcMethods(array $methods)
        {
            $methods = [];
            return $methods;
        }// disableXmlrpcMethods


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_xmlrpc']) && $rundizable_wp_features_optname['disable_xmlrpc'] == '1') {
                add_filter('xmlrpc_enabled', '__return_false');
                add_filter('xmlrpc_methods', [$this, 'disableXmlrpcMethods']);
                add_filter('wp_headers', [$this, 'disableXmlrpcHeaders'], 10, 2);
                add_filter('bloginfo_url', [$this, 'removePingbackURLFromBloginfo'], 10, 2);
                add_filter('pings_open', '__return_false', 10, 2);
                add_action('wp', [$this, 'removeLinkEditURI']);
            }
        }// registerHooks


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
         * @param string $output
         * @param string $show
         * @return string
         */
        public function removePingbackURLFromBloginfo($output, $show)
        {
            return ('pingback_url' === $show ? null : $output);
        }// removePingbackURLFromBloginfo


    }// DisableXMLRPC
}// endif;
