<?php /*<div class="wrap">
    <h1><?php _e('Example of how to access settings values in option db.', 'rundizable-wp-features'); ?></h1>

    <ol>
        <li><?php printf(__('Call to <code>%s</code>', 'rundizable-wp-features'), '$this->getOptions();'); ?></li>
        <li><?php printf(__('Access this variable as global <code>%s</code>. This variable will be change, up to config in AppTrait.', 'rundizable-wp-features'), 'global $rundizable_wp_features_optname;'); ?></li>
        <li><?php _e('Now, you can use this variable to access its array key anywhere.', 'rundizable-wp-features'); ?></li>
    </ol>
    <h3>Example: <code>print_r($rundizable_wp_features_optname);</code></h3>
    <pre style="background-color: #333; border: 1px solid #ccc; color: #ddd; height: 500px; overflow: auto; padding: 10px;"><?php 
        if (isset($rundizable_wp_features_optname)) {
            echo htmlspecialchars(print_r($rundizable_wp_features_optname, true), ENT_QUOTES, get_option('blog_charset')); 
        }
    ?></pre>
</div>*/