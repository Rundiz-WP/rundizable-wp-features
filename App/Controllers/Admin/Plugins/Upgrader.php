<?php
/**
 * Upgrade or update the plugin action.
 *
 * @package Rundizable-WP-Features
 * @since 1.0.3 Moved from part of App/Controllers/Admin/Activation.php
 */


namespace RundizableWpFeatures\App\Controllers\Admin\Plugins;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\RundizableWpFeatures\\App\\Controllers\\Admin\\Plugins\\Upgrader')) {
    /**
     * Plugin upgrader class.
     */
    class Upgrader implements \RundizableWpFeatures\App\Controllers\ControllerInterface
    {


        use \RundizableWpFeatures\App\AppTrait;


        /**
         * @var string The current admin page.
         */
        private $hookSuffix = '';


        /**
         * Ajax manual update.
         */
        public function ajaxManualUpdate()
        {
            if (!current_user_can('update_plugins')) {
                wp_die(
                    esc_html__('You do not have permission to access this page.', 'rundizable-wp-features'), 
                    '', 
                    ['response' => 403]
                );
            }

            $output = [];

            // phpcs:ignore WordPress.Security
            if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post' && isset($_POST) && !empty($_POST)) {
                // if method POST and there is POST data.
                if (check_ajax_referer('rundizable_wp_features_nonce', 'security', false) === false) {
                    status_header(403);
                    wp_die(
                        esc_html__('Please reload this page and try again.', 'rundizable-wp-features'), 
                        '', 
                        ['response' => 403]
                    );
                }

                $updateKey = filter_input(INPUT_POST, 'updateKey', FILTER_SANITIZE_NUMBER_INT);

                $Loader = new \RundizableWpFeatures\App\Libraries\Loader();
                $manualUpdateClasses = $Loader->getManualUpdateClasses();
                $maxManualUpdateVersion = 0;
                unset($Loader);

                if (is_array($manualUpdateClasses) && array_key_exists($updateKey, $manualUpdateClasses) && class_exists($manualUpdateClasses[$updateKey])) {
                    $UpdateClass = new $manualUpdateClasses[$updateKey]();

                    try {
                        $UpdateClass->run();// run a manual update single action
                    } catch (\Exception $e) {
                        $errorMessage = $e->getMessage();
                    }

                    if (!isset($errorMessage) || empty($errorMessage)) {
                        $lastError = error_get_last();
                        if (!empty($lastError)) {
                            if (is_array($lastError) && array_key_exists('message', $lastError) && is_scalar($lastError['message'])) {
                                $errorMessage = $lastError['message'];
                            } else {
                                $errorMessage = __('An error has been occur, cannot continue manual update. Please contact plugin author.', 'rundizable-wp-features');
                            }
                        }
                        unset($lastError);
                    }

                    if (!isset($errorMessage) || (isset($errorMessage) && empty($errorMessage))) {
                        // if there is no error.
                        if (version_compare($maxManualUpdateVersion, $UpdateClass->manual_update_version, '<')) {
                            $maxManualUpdateVersion = $UpdateClass->manual_update_version;
                        }

                        $output['alreadyRunKey'] = $updateKey;
                        $output['alreadyRunClass'] = $manualUpdateClasses[$updateKey];
                        $output['formResultClass'] = 'notice-success';
                        if (array_key_exists(($updateKey + 1), $manualUpdateClasses)) {
                            $output['nextRunKey'] = ($updateKey + 1);
                            $output['formResultMsg'] = __('Success, please click next to continue update.', 'rundizable-wp-features');
                        } else {
                            $output['nextRunKey'] = 'end';
                            $output['formResultMsg'] = __('All manual update completed successfully. This page will be no longer available until there is next manual update.', 'rundizable-wp-features');

                            $currentConfig = $this->getOptions();
                            $currentConfig['rdsfw_manual_update_version'] = $maxManualUpdateVersion;
                            $this->saveOptions($currentConfig);
                            unset($currentConfig);

                            delete_transient('rundizable_wp_features_updated');
                        }
                    } else {
                        // if contain error.
                        status_header(500);
                        $output['formResultClass'] = 'notice-error';
                        $output['formResultMsg'] = $errorMessage;
                    }
                    unset($errorMessage, $UpdateClass);
                } else {
                    status_header(501);
                    $output['formResultClass'] = 'notice-error';
                    $output['formResultMsg'] = __('Unable to run update, there is no update classes to run.', 'rundizable-wp-features');
                }

                unset($manualUpdateClasses, $maxManualUpdateVersion, $updateKey);
            }// endif;

            wp_send_json($output);
        }// ajaxManualUpdate


        /**
         * Allow code/WordPress to call hook `admin_enqueue_scripts` 
         * then `wp_register_script()`, `wp_localize_script()`, `wp_enqueue_script()` functions will be working fine later.
         * 
         * @link https://wordpress.stackexchange.com/a/76420/41315 Original source code.
         */
        public function callEnqueueHook()
        {
            add_action('admin_enqueue_scripts', [$this, 'registerStyles']);
            add_action('admin_enqueue_scripts', [$this, 'registerScripts']);
        }// callEnqueueHook


        /**
         * Detect this plugin updated and display link or maybe redirect to manual update page.
         *
         * This method will be run as new version of code.<br>
         * To understand more about new version of code, please read more on `updateProcessComplete()` method.
         *
         * @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices Reference.
         */
        public function detectPluginUpdate()
        {
            if (get_transient('rundizable_wp_features_updated') && current_user_can('update_plugins')) {
                // if there is updated transient
                $Loader = new \RundizableWpFeatures\App\Libraries\Loader();

                if ($Loader->haveManualUpdate() === true) {
                    // if found that there are manual update in this new version of code.
                    // display link or redirect to manual update page. (display link is preferred to prevent bad user experience.)
                    // -------------------------------------------------------------------------------------
                    // display link to manual update page.
                    // phpcs:ignore WordPress.Security.NonceVerification
                    if (!isset($_REQUEST['page']) || (isset($_REQUEST['page']) && 'rundizable-wp-features-manual-update' !== $_REQUEST['page'])) {
                        $manualUpdateNotice = '<div class="notice notice-warning is-dismissible">
                            <p>' .
                                sprintf(
                                    // translators: %1$s Open link, %2$s Close link.
                                    esc_html__('The Rundizable WP Features is just upgraded and need to be manually update. Please continue to the %1$splugin update page%2$s.', 'rundizable-wp-features'),
                                    '<a href="' . esc_url(network_admin_url('index.php?page=rundizable-wp-features-manual-update')) . '">', // this link will be auto convert to admin_url if not in multisite installed.
                                    '</a>'
                                ) .
                            '</p>
                        </div>';

                        add_action('admin_notices', function () use ($manualUpdateNotice) {
                            // the line below will be echo out custom HTML. So, it cannot be and must not escape or the result will be broken.
                            echo $manualUpdateNotice . "\n";// phpcs:ignore WordPress.Security.EscapeOutput
                        });
                        add_action('network_admin_notices', function () use ($manualUpdateNotice) {
                            // the line below will be echo out custom HTML. So, it cannot be and must not escape or the result will be broken.
                            echo $manualUpdateNotice . "\n";// phpcs:ignore WordPress.Security.EscapeOutput
                        });

                        unset($manualUpdateNotice);
                    }// endif;

                    if (is_multisite()) {
                        add_action('network_admin_menu', [$this, 'displayManualUpdateMenu']);
                    } else {
                        add_action('admin_menu', [$this, 'displayManualUpdateMenu']);
                    }

                    add_action('wp_ajax_rundizable_wp_features_manualUpdate', [$this, 'ajaxManualUpdate']);
                    // end display link to manual update page.
                    // -------------------------------------------------------------------------------------
                } else {
                    // if don't have any manual update.
                    delete_transient('rundizable_wp_features_updated');
                }// endif;

                unset($Loader);
            }// endif;
        }// detectPluginUpdate


        /**
         * Setup manual update page and must be added to admin menu. In this case, add as sub menu of dashboard menu.
         */
        public function displayManualUpdateMenu()
        {
            $hook_suffix = add_dashboard_page(__('Rundizable WP Features update', 'rundizable-wp-features'), __('Rundizable WP Features update', 'rundizable-wp-features'), 'update_plugins', 'rundizable-wp-features-manual-update', [$this, 'displayManualUpdatePage']);
            if (is_string($hook_suffix)) {
                $this->hookSuffix = $hook_suffix;
                add_action('load-' . $hook_suffix, [$this, 'callEnqueueHook']);
            }
            unset($hook_suffix);
        }// displayManualUpdateMenu


        /**
         * Display manual update page.
         */
        public function displayManualUpdatePage()
        {
            if (!current_user_can('update_plugins')) {
                wp_die(esc_html__('You do not have permission to access this page.', 'rundizable-wp-features'));
            }

            $output = [];

            $Loader = new \RundizableWpFeatures\App\Libraries\Loader();
            $output['manualUpdateClasses'] = $Loader->getManualUpdateClasses();

            $Loader->loadView('admin/Plugins/Upgrader_v', $output);
            unset($Loader, $output);
        }// displayManualUpdatePage


        /**
         * {@inheritDoc}
         *
         * @todo [rd-settings-fw] in your project, if you don't use manual update process then please comment all code in this method can improve performance.
         */
        public function registerHooks()
        {
            // On update/upgrade plugin completed, set transient and let `detectPluginUpdate()` work.
            add_action('upgrader_process_complete', [$this, 'updateProcessComplete'], 10, 2);
            // On WordPress has finished loading but before any headers are sent, display link or maybe redirect to manual update page.
            add_action('init', [$this, 'detectPluginUpdate']);
        }// registerHooks


        /**
         * Enqueue CSS & JS.
         *
         * This method was called from displayManualUpdateMenu which is active only when plugin is just updated.
         * 
         * @param string $hook_suffix The current admin page.
         */
        public function registerScripts($hook_suffix = '')
        {
            if ($hook_suffix !== $this->hookSuffix) {
                return;
            }

            wp_localize_script(
                'rundizable-wp-features-handle-rd-settings-manual-update',
                'RundizableWpFeaturesRdSettingsManualUpdate',
                [
                    'alreadyRunUpdateKey' => '',
                    'alreadyRunUpdateTotal' => 0,
                    'completed' => 'false',
                    'nonce' => wp_create_nonce('rundizable_wp_features_nonce'),
                    'txtCompleted' => __('Completed', 'rundizable-wp-features'),
                    'txtDismissNotice' => __('Dismiss', 'rundizable-wp-features'),
                    'txtNext' => __('Next', 'rundizable-wp-features'),
                ]
            );

            wp_enqueue_script('rundizable-wp-features-font-awesome5');
            
            $Loader = new \RundizableWpFeatures\App\Libraries\Loader();
            $manualUpdateClasses = $Loader->getManualUpdateClasses();
            unset($Loader);
            wp_add_inline_script('rundizable-wp-features-handle-rd-settings-manual-update', 'var manualUpdateClasses = ' . (!empty($manualUpdateClasses) ? wp_json_encode($manualUpdateClasses) : '') . ';');
            unset($manualUpdateClasses);

            wp_enqueue_script('rundizable-wp-features-handle-rd-settings-manual-update');
        }// registerScripts


        /**
         * Enqueue only CSS.
         * 
         * @param string $hook_suffix The current admin page.
         */
        public function registerStyles($hook_suffix = '')
        {
            if ($hook_suffix !== $this->hookSuffix) {
                return;
            }

            wp_enqueue_style('rundizable-wp-features-font-awesome5');
        }// registerStyles


        /**
         * After update plugin completed.
         *
         * This method will be called while running the current version of this plugin, not the new one that just updated.
         * For example: You are running 1.0 and just updated to 2.0. The 2.0 version will not working here yet but 1.0 is working.
         * So, any code here will not work as the new version. Please be aware!
         *
         * This method will add the transient to be able to detect updated and run the manual update in `detectPluginUpdate()` method.
         *
         * @link https://developer.wordpress.org/reference/hooks/upgrader_process_complete/ Reference.
         * @link https://developer.wordpress.org/reference/classes/wp_upgrader/ Reference.
         * @param \WP_Upgrader $upgrader The `\WP_Upgrader` class.
         * @param array $hook_extra Array of bulk item update data.
         */
        public function updateProcessComplete(\WP_Upgrader $upgrader, array $hook_extra)
        {
            if (is_array($hook_extra) && array_key_exists('action', $hook_extra) && array_key_exists('type', $hook_extra) && array_key_exists('plugins', $hook_extra)) {
                if ('update' === $hook_extra['action'] && 'plugin' === $hook_extra['type'] && is_array($hook_extra['plugins']) && !empty($hook_extra['plugins'])) {
                    $this_plugin = plugin_basename(RUNDIZABLEWPFEATURES_FILE);
                    foreach ($hook_extra['plugins'] as $key => $plugin) {
                        if ($this_plugin === $plugin) {
                            // if this plugin is in the updated plugins.
                            // set transient to let it run later. this transient will be called and run in `detectPluginUpdate()` method.
                            set_transient('rundizable_wp_features_updated', 1);
                            break;
                        }
                    }// endforeach;
                    unset($key, $plugin, $this_plugin);
                }// endif update plugin and plugins not empty.
            }// endif; $hook_extra
        }// updateProcessComplete


    }// Upgrader
}
