<?php // phpcs:disable

/*
if (!defined('ABSPATH')) {
    exit();
}


?>
<div class="wrap">
    <h1><?php _e('Example of how to access settings values in option db.', 'rundizable-wp-features'); ?></h1>

    <ol>
        <li><?php printf(
            // translators: %s PHP code.
            esc_html__('Call to %s', 'rundizable-wp-features'),
            '<code>$this->getOptions();</code>'
        ); ?></li>
        <li><?php printf(
            // translators: %s PHP code.
            esc_html__('Access this variable as global %s. This variable will be change, up to config in AppTrait.', 'rundizable-wp-features'),
            '<code>global $plugin_template_optname;</code>'
        ); ?></li>
        <li><?php esc_html_e('Now, you can use this variable to access its array key anywhere.', 'rundizable-wp-features'); ?></li>
    </ol>
    <h3>Example: <code>print_r($rundizable_wp_features_optname);</code></h3>
    <pre style="background-color: #333; border: 1px solid #ccc; color: #ddd; height: 500px; overflow: auto; padding: 10px;"><?php 
    if (isset($rundizable_wp_features_optname)) {
        echo esc_html(print_r($rundizable_wp_features_optname, true)); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
    }
    ?></pre>
    <p><?php esc_html_e('To see real data before escape, please use view source to see them below this line.', 'rundizable-wp-features'); ?></p>
    <!--
    raw data from DB (below this line):
    <?php print_r($plugin_template_optname); // phpcs:ignore WordPress.PHP.DevelopmentFunctions ?> 
    -->
</div>
<?php
/**/
