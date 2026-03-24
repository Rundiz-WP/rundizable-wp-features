<?php
/**
 * Settings view file.
 * 
 * @package Rundizable-WP-Features
 */


if (!defined('ABSPATH')) {
    exit();
}

$rundizable_wp_features_kses_file = dirname(RUNDIZABLEWPFEATURES_FILE) . '/App/config/kses_data.php';
?>
<div class="wrap">
    <h1><?php esc_html_e('Rundizable WP Features settings', 'rundizable-wp-features'); ?></h1>

    <?php if (isset($form_result_class) && isset($form_result_msg)) { ?> 
    <div class="<?php echo esc_attr($form_result_class); ?> notice is-dismissible">
        <p>
            <strong><?php echo esc_html($form_result_msg); ?></strong>
        </p>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'rundizable-wp-features'); ?></span></button>
    </div>
    <?php } ?> 

    <form method="post">
        <?php wp_nonce_field(); ?> 
        <?php 
        if (isset($settings_page)) {
            if (!is_file($rundizable_wp_features_kses_file)) {
                // if not found custom kses data. use custom kses data to make sure it is up to date with modern HTML elements and attributes that will work.
                // if not found then it should shown the error message, without translation because If this happens to a user from an unknown language, assistance may not be possible.
                throw new \Exception(esc_html('The file ' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $rundizable_wp_features_kses_file) . ' could not be found.'));
            }
            echo wp_kses($settings_page, include $rundizable_wp_features_kses_file);
        } 
        ?> 
        <?php submit_button(); ?> 
    </form>
</div><!--.wrap-->