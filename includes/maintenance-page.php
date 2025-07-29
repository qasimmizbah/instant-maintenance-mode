<?php
/**
 * Frontend maintenance mode template
 */

function immod_maintenance_page() {
    $options = get_option('immod_settings');
    
    // Enqueue frontend CSS
    wp_enqueue_style(
        'immod-frontend-css',
        IMMOD_PLUGIN_URL . 'assets/css/frontend.css',
        array(),
        IMMOD_PLUGIN_VERSION
    );
    
    // Get logo or site name
    $logo_html = '';
    if (!empty($options['logo'])) {
        $logo_html = '<div class="immod-logo"><img src="' . esc_url($options['logo']) . '" alt="' . esc_attr(get_bloginfo('name')) . '"></div>';
    } else {
        $logo_html = '<h1 class="immod-site-title">' . esc_html(get_bloginfo('name')) . '</h1>';
    }
    
    // Display maintenance page
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php esc_html_e('Maintenance Mode', 'instant-maintenance-mode'); ?> - <?php bloginfo('name'); ?></title>
        <?php wp_head(); ?>
    </head>
    <body class="immod-maintenance-body">
        <div class="immod-maintenance-container">
            <?php echo wp_kses_post($logo_html); ?>
            <h1 class="immod-maintenance-title"><?php esc_html_e('Maintenance Mode', 'instant-maintenance-mode'); ?></h1>
            <p class="immod-maintenance-message"><?php echo esc_html($options['message'] ?? __('Website under maintenance. Please check back soon.', 'instant-maintenance-mode')); ?></p>
        </div>
        <?php wp_footer(); ?>
    </body>
    </html>
    <?php
    exit;
}