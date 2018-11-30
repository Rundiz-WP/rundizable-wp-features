<?php
/**
 * Loader class. This class will load anything for example: views, template, configuration file.
 * 
 * @package Rundizable-WP-Features
 */


namespace RundizableWpFeatures\App\Libraries;

if (!class_exists('\\RundizableWpFeatures\\App\\Libraries\\Loader')) {
    class Loader
    {


        /**
         * Automatic look into those controllers and register to the main App class to make it works.<br>
         * The controllers that will be register must implement RundizableWpFeatures\App\Controllers\ControllerInterface to have registerHooks() method in it, otherwise it will be skipped.
         */
        public function autoRegisterControllers()
        {
            $this_plugin_dir = dirname(RUNDIZABLEWPFEATURES_FILE);
            $di = new \RecursiveDirectoryIterator($this_plugin_dir . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Controllers', \RecursiveDirectoryIterator::SKIP_DOTS);
            $it = new \RecursiveIteratorIterator($di);
            unset($di);

            $file_list = [];
            foreach ($it as $file) {
                $file_list[] = $file;
            }// endforeach;
            unset($file, $it);
            natsort($file_list);

            foreach ($file_list as $file) {
                $this_file_classname = '\\RundizableWpFeatures' . str_replace([$this_plugin_dir, '.php', '/'], ['', '', '\\'], $file);
                if (class_exists($this_file_classname)) {
                    $TestClass = new \ReflectionClass($this_file_classname);
                    if (!$TestClass->isAbstract()) {
                        $ControllerClass = new $this_file_classname();
                        if (method_exists($ControllerClass, 'registerHooks')) {
                            $ControllerClass->registerHooks();
                        }
                        unset($ControllerClass);
                    }
                    unset($TestClass);
                }
                unset($this_file_classname);
            }// endforeach;

            unset($file, $file_list, $this_plugin_dir);
        }// autoRegisterControllers


        /**
         * load config file and return its values.
         * 
         * @param string $config_file_name
         * @param boolean $require_once
         * @return mixed return config file content if success. return false if failed.
         */
        public function loadConfig($config_file_name = 'config', $require_once = false)
        {
            $config_dir = dirname(__DIR__).'/config/';

            if ($config_dir != null && file_exists($config_dir) && is_file($config_dir.$config_file_name.'.php')) {
                if ($require_once === true) {
                    $config_values = require_once $config_dir.$config_file_name.'.php';
                } else {
                    $config_values = require $config_dir.$config_file_name.'.php';
                }
            }

            unset($config_dir);
            if (isset($config_values)) {
                return $config_values;
            }
            return false;
        }// loadConfig


        /**
         * load views.
         * 
         * @param string $view_name view file name refer from app/Views folder.
         * @param array $data for send data variable to view.
         * @param boolean $require_once use include or include_once? if true, use include_once.
         * @return boolean return true if success loading, or return false if failed to load.
         */
        public function loadView($view_name, array $data = [], $require_once = false)
        {
            $view_dir = dirname(__DIR__).'/Views/';

            if ($view_name != null && file_exists($view_dir.$view_name.'.php') && is_file($view_dir.$view_name.'.php')) {
                if (is_array($data)) {
                    extract($data, EXTR_PREFIX_SAME, 'dupvar_');
                }

                if ($require_once === true) {
                    include_once $view_dir.$view_name.'.php';
                } else {
                    include $view_dir.$view_name.'.php';
                }

                unset($view_dir);
                return true;
            }

            unset($view_dir);
            return false;
        }// loadView


    }
}