<?php
/**
 * @since 0.2.7
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizableWpFeatures\App\Controllers\Hooks;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Hooks\\DisableAuthorPage')) {
    /**
     * Disable author page.
     * 
     * @since 0.2.7
     */
    class DisableAuthorPage implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        public function __construct()
        {
            $this->getOptions();
        }// __construct


        /**
         * Redirect author page.
         */
        public function disableAuthorPage()
        {
            if (is_author()) {
                nocache_headers();
                wp_safe_redirect(home_url(), 301);
                exit();
            }
        }// disableAuthorPage


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            global $rundizable_wp_features_optname;
            if (isset($rundizable_wp_features_optname['disable_authorpage_front']) && $rundizable_wp_features_optname['disable_authorpage_front'] == '1') {
                add_action('template_redirect', [$this, 'disableAuthorPage']);
                add_filter('author_link', [$this, 'removeAuthorLink'], 10, 3);
            }
        }// registerHooks


        /**
         * Remove author's link.
         * 
         * @param string $link The URL to the author’s page.
         * @param int $author_id The author’s ID.
         * @param string $author_nicename The author’s nice name.
         * @return string
         */
        public function removeAuthorLink($link, $author_id, $author_nicename)
        {
            return home_url();
        }// removeAuthorLink


    }// DisableAuthorPage
}// endif;
