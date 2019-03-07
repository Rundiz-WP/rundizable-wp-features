<?php
/**
 * Activation class is the class that will be working on activate, deactivate, delete WordPress plugin.
 * 
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App\Controllers\Admin;

if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Admin\\Activation')) {
    class Activation implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        /**
         * controller constructor
         */
        public function __construct() {
            // setup all options from setting config file.
            $this->setupAllOptions();
        }// __construct


        /**
         * add links to plugin actions area
         * 
         * @param array $actions current plugin actions. (including deactivate, edit).
         * @param string $plugin_file the plugin file for checking.
         * @return array return modified links
         */
        public function actionLinks($actions, $plugin_file)
        {
            static $plugin;
            
            if (!isset($plugin)) {
                $plugin = plugin_basename(RUNDIZABLEWPFEATURES_FILE);
            }
            
            if ($plugin == $plugin_file) {
                $link['settings'] = '<a href="'.  esc_url(get_admin_url(null, 'options-general.php?page=rundizable-wp-features-settings')).'">'.__('Settings').'</a>';
                $actions = array_merge($link, $actions);
                //$actions['after_actions'] = '<a href="#" onclick="return false;">'.__('After Actions', 'rundizable-wp-features').'</a>';
            }
            
            return $actions;
        }// actionLinks


        /**
         * activate the plugin by admin on wp plugin page.
         * 
         * @global \wpdb $wpdb WordPress db class.
         */
        public function activation()
        {
            // do something that will happens on activate plugin.
            // @todo [rd-settings-fw] In real project, you may need to verify PHP version and/or WordPress version before did activation.
            $wordpress_required_version = '4.6.0';
            $phpversion_required = '5.5';
            if (function_exists('phpversion')) {
                $phpversion = phpversion();
            }
            if (!isset($phpversion) || (isset($phpversion) && $phpversion === false)) {
                if (defined('PHP_VERSION')) {
                    $phpversion = PHP_VERSION;
                } else {
                    // can't detect php version
                    $phpversion = '4';
                }
            }
            if (version_compare($phpversion, $phpversion_required, '<')) {
                /* translators: %1$s: Current PHP version, %2$s: Required PHP version. */
                wp_die(sprintf(__('You are using PHP %1$s which does not meet minimum requirement. Please consider upgrade PHP version or contact plugin author for this help.<br><br>Minimum requirement:<br>PHP %2$s', 'rundizable-wp-features'), $phpversion, $phpversion_required), __('Minimum requirement of PHP version does not meet.', 'rundizable-wp-features'));
                exit;
            }
            if (version_compare(get_bloginfo('version'), $wordpress_required_version, '<')) {
                /* translators: %1$s: Current WordPress version, %2$s: Required WordPress version. */
                wp_die(sprintf(__('Your WordPress version does not meet the requirement. (%1$s < %2$s).', 'rundizable-wp-features'), get_bloginfo('version'), $wordpress_required_version));
                exit;
            }
            unset($phpversion, $phpversion_required, $wordpress_required_version);

            // get wpdb global var.
            global $wpdb;
            $wpdb->show_errors();

            // get current options for use incase it is update.
            $rundizable_wp_features_optname = $this->getOptions();

            // add option to site or multisite -----------------------------
            if (is_multisite()) {
                // this site is multisite. activate on all site.
                $blog_ids = $wpdb->get_col('SELECT blog_id FROM '.$wpdb->blogs);
                $original_blog_id = get_current_blog_id();
                if ($blog_ids) {
                    foreach ($blog_ids as $blog_id) {
                        switch_to_blog($blog_id);
                        $this->activationAddUpdateOption($rundizable_wp_features_optname);
                    }
                }
                switch_to_blog($original_blog_id);
                unset($blog_id, $blog_ids, $original_blog_id);
            } else {
                // this site is single site. activate on single site.
                $this->activationAddUpdateOption($rundizable_wp_features_optname);
            }

            unset($rundizable_wp_features_optname);
        }// activation


        /**
         * check for option on current site and add if not exists, or update if option is older.
         * 
         * @param array $current_options current options values for check and use in case of update options.
         */
        private function activationAddUpdateOption(array $current_options = [])
        {
            // check current option exists or not.
            $current_options = get_option($this->main_option_name);

            if ($current_options === false) {
                // current option is not exists, add it.
                $sub_options = [];
                
                if (is_array($this->all_options) && !empty($this->all_options)) {
                    $sub_options = $this->all_options;
                }
                
                $sub_options = maybe_serialize($sub_options);
                add_option($this->main_option_name, $sub_options);
                unset($sub_options);
            } elseif (isset($some_of_your_update_options_conditions) && $some_of_your_update_options_conditions === true) {
                // use update if some condition is met. such as older options.
                // @todo [rd-settings-fw] In real project, change update options on activate to use "your condition" at "elseif" above.
                $sub_options = $current_options;
                $sub_options = maybe_serialize($sub_options);
                update_option($this->main_option_name, $sub_options);
                unset($sub_options);
            }

            unset($current_options);
        }// activationAddUpdateOption


        /**
         * deactivate the plugin hook.
         */
        public function deactivation()
        {
            // do something that will be happens on deactivate plugin.
        }// deactivation


        /**
         * get main_option_name from trait which is non-static from any static method.
         * 
         * @return string
         */
        private static function getMainOptionName()
        {
            $class = new self;
            return $class->main_option_name;
        }// getMainOptionName


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // register activate hook
            register_activation_hook(RUNDIZABLEWPFEATURES_FILE, [&$this, 'activation']);
            // register deactivate hook
            register_deactivation_hook(RUNDIZABLEWPFEATURES_FILE, [&$this, 'deactivation']);
            // register uninstall hook. this hook will be work on delete plugin.
            // * register uninstall hook MUST be static method or function.
            register_uninstall_hook(RUNDIZABLEWPFEATURES_FILE, array('\\RundizableWpFeatures\\App\\Controllers\\Admin\\Activation', 'uninstall'));
            // on update/upgrade plugin
            add_action('upgrader_process_complete', [$this, 'updatePlugin'], 10, 2);

            // add filter action links. this will be displayed in actions area of plugin page. for example: xxxbefore | Activate | Edit | Delete | xxxafter
            add_filter('plugin_action_links', [&$this, 'actionLinks'], 10, 5);
            // add filter to row meta. (in plugin page below description)
            add_filter('plugin_row_meta', [&$this, 'rowMeta'], 10, 2);
        }// registerHooks


        /**
         * add links to row meta that is in plugin page under plugin description.
         * 
         * @staticvar string $plugin the plugin file name.
         * @param array $links current meta links
         * @param string $file the plugin file name for checking.
         * @return array return modified links.
         */
        public function rowMeta($links, $file)
        {
            static $plugin;
            
            if (!isset($plugin)) {
                $plugin = plugin_basename(RUNDIZABLEWPFEATURES_FILE);
            }
            
            if ($plugin === $file) {
                $new_link[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9HQE4GVV4KTZE" target="donate-rdawpf">' . __('Donate', 'rundizable-wp-features') . '</a>';
                $links = array_merge($links, $new_link);
                unset($new_link);
            }
            
            return $links;
        }// rowMeta


        /**
         * delete the plugin.
         * 
         * @global \wpdb $wpdb
         */
        public static function uninstall()
        {
            // do something that will be happens on delete plugin.
            global $wpdb;
            $wpdb->show_errors();

            // delete options.
            if (is_multisite()) {
                // this is multi site, delete options in all sites.
                $blog_ids = $wpdb->get_col('SELECT blog_id FROM '.$wpdb->blogs);
                $original_blog_id = get_current_blog_id();
                if ($blog_ids) {
                    foreach ($blog_ids as $blog_id) {
                        switch_to_blog($blog_id);
                        delete_option(static::getMainOptionName());
                    }
                }
                switch_to_blog($original_blog_id);
                unset($blog_id, $blog_ids, $original_blog_id);
            } else {
                // this is single site, delete options in single site.
                delete_option(static::getMainOptionName());
            }
        }// uninstall


        /**
         * Works on update plugin.
         * 
         * @link https://developer.wordpress.org/reference/hooks/upgrader_process_complete/ Reference.
         * @param \WP_Upgrader $upgrader
         * @param array $hook_extra
         */
        public function updatePlugin(\WP_Upgrader $upgrader, array $hook_extra)
        {
            if (is_array($hook_extra) && array_key_exists('action', $hook_extra) && array_key_exists('type', $hook_extra) && array_key_exists('plugins', $hook_extra)) {
                if ($hook_extra['action'] == 'update' && $hook_extra['type'] == 'plugin' && is_array($hook_extra['plugins']) && !empty($hook_extra['plugins'])) {
                    $this_plugin = plugin_basename(RUNDIZABLEWPFEATURES_FILE);
                    foreach ($hook_extra['plugins'] as $key => $plugin) {
                        if ($this_plugin == $plugin) {
                            $this_plugin_updated = true;
                            break;
                        }
                    }// endforeach;
                    unset($key, $plugin, $this_plugin);

                    if (isset($this_plugin_updated) && $this_plugin_updated === true) {
                        // get wpdb global var.
                        global $wpdb;
                        $wpdb->show_errors();

                        // get current options for use incase it is update.
                        $rundizable_wp_features_optname = $this->getOptions();

                        // add option to site or multisite -----------------------------
                        if (is_multisite()) {
                            // this site is multisite. activate on all site.
                            $blog_ids = $wpdb->get_col('SELECT blog_id FROM '.$wpdb->blogs);
                            $original_blog_id = get_current_blog_id();
                            if ($blog_ids) {
                                foreach ($blog_ids as $blog_id) {
                                    switch_to_blog($blog_id);
                                    $this->activationAddUpdateOption($rundizable_wp_features_optname);
                                }
                            }
                            switch_to_blog($original_blog_id);
                            unset($blog_id, $blog_ids, $original_blog_id);
                        } else {
                            // this site is single site. activate on single site.
                            $this->activationAddUpdateOption($rundizable_wp_features_optname);
                        }

                        unset($rundizable_wp_features_optname);
                    }// endif; $this_plugin_updated
                }// endif update plugin and plugins not empty.
            }// endif; $hook_extra
        }// updatePlugin


    }
}