<?php
/**
 * Activate the plugin action.
 *
 * @package Rundizable-WP-Features
 * @since 1.0.3 Moved from App/Controllers/Admin/Activation.php
 */


namespace RundizableWpFeatures\App\Controllers\Admin\Plugins;


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Admin\\Plugins\\Activation')) {
    /**
     * Plugin activation and new site activation hooks class.
     */
    class Activation implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        /**
         * Activate the plugin by admin on WP plugin page.
         *
         * @link https://developer.wordpress.org/reference/functions/register_activation_hook/ The function `register_activation_hook()` reference.
         * @link https://developer.wordpress.org/reference/hooks/activate_plugin/ The reference about what will be pass to callback of function `register_activation_hook()`.
         * @global \wpdb $wpdb WordPress DB class.
         * @param bool $network_wide Whether to enable the plugin for all sites in the network or just the current site. Multisite only. Default false.
         * @throws \Exception Throw the exception if failed to detect current version of PHP.
         */
        public function activate($network_wide)
        {
            // So something that will happens on activate plugin.
            // @todo [rd-settings-fw] In your project, you may need to verify PHP version and/or WordPress version before did activate.
            $wordpress_required_version = '4.6.0';
            $phpversion_required = '5.5';
            if (function_exists('phpversion')) {
                $phpversion = phpversion();
            }
            if (!isset($phpversion) || (isset($phpversion) && false === $phpversion)) {
                if (defined('PHP_VERSION')) {
                    $phpversion = PHP_VERSION;
                } else {
                    // if there is no defined constant `PHP_VERSION`.
                    // @link https://www.php.net/ChangeLog-4.php Reference.
                    throw new \Exception('You are using ancient version of PHP. The constant `PHP_VERSION` is available since PHP 4.0.');
                }
            }
            if (version_compare($phpversion, $phpversion_required, '<')) {
                wp_die(
                    esc_html(
                        sprintf(
                            /* translators: %1$s current PHP version. */
                            __('You are using PHP %1$s which does not meet minimum requirement. Please consider upgrade PHP version or contact plugin author for this help.', 'rundizable-wp-features'),
                            $phpversion
                        )
                    )
                    . '<br><br>'
                    . esc_html(
                        sprintf(
                            /* translators: %1$s minimum PHP version required. */
                            __('Minimum PHP requirement: %1$s.', 'rundizable-wp-features'),
                            $phpversion_required
                        )
                    ), 
                    esc_html__('Minimum requirement of PHP version does not meet.', 'rundizable-wp-features')
                );
                exit(1);
            }// endif;
            if (version_compare(get_bloginfo('version'), $wordpress_required_version, '<')) {
                wp_die(
                    esc_html(
                        sprintf(
                            // translators: %1$s Current WordPress version, %2$s Required WordPress version.
                            __('Your WordPress version does not meet the requirement. (%1$s < %2$s).', 'rundizable-wp-features'), 
                            get_bloginfo('version'),
                            $wordpress_required_version
                        )
                    ),
                    esc_html__('Minimum requirement of WordPress version does not meet.', 'rundizable-wp-features')
                );
                exit(1);
            }// endif;
            unset($phpversion, $phpversion_required, $wordpress_required_version);

            // Get `$wpdb` global var.
            global $wpdb;
            $wpdb->show_errors();

            // Add option to site or multisite -----------------------------
            if (is_multisite()) {
                // This site is multisite. Add/update options, create/alter tables on all sites.
                $blog_ids = get_sites(['fields' => 'ids', 'number' => 0]);
                $original_blog_id = get_current_blog_id();
                if ($blog_ids) {
                    foreach ($blog_ids as $blog_id) {
                        switch_to_blog($blog_id);
                        $this->activateAddUpdateOption();
                    }
                }
                switch_to_blog($original_blog_id);
                unset($blog_id, $blog_ids, $original_blog_id);
            } else {
                // this site is single site. activate on single site.
                        $this->activateAddUpdateOption();
            }
        }// activate


        /**
         * Check if the options was added before or not, if not then add the options otherwise update them.
         */
        private function activateAddUpdateOption()
        {
            // check current option exists or not.
            $current_options = get_option($this->main_option_name);

            if (false === $current_options) {
                // if this is newly activate. it is never activated before, add the options.
                $this->setupAllOptions();
                $this->saveOptions($this->all_options);
            }// endif;

            unset($current_options);
        }// activateAddUpdateOption


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // register activate hook
            register_activation_hook(RUNDIZABLEWPFEATURES_FILE, [$this, 'activate']);

            // hook on create new site (removed).
        }// registerHooks


    }// Activation
}
