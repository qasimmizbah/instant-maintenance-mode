<?php
/**
 * The core plugin class that maintains all the functionality.
 */

class Immod_Instant_Maintenance_Mode {

    public function __construct() {
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Initialize settings
        add_action('admin_init', array($this, 'settings_init'));
        
        // Check maintenance mode
        add_action('template_redirect', array($this, 'check_maintenance_mode'));
        
        // Enqueue admin styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }

    // Enqueue admin styles
    public function enqueue_admin_styles($hook) {
        if ($hook != 'toplevel_page_instant_maintenance_mode') {
            return;
        }
        
        wp_enqueue_style(
            'immode-instant-maintenance-mode-admin',
            IMMOD_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            IMMOD_PLUGIN_VERSION
        );
    }

    // Add admin menu item
    public function add_admin_menu() {
        add_menu_page(
            'Instant Maintenance Mode',
            'Instant Maintenance',
            'manage_options',
            'immod_instant_maintenance_mode',
            array($this, 'settings_page'),
            'dashicons-hammer',
            80
        );
    }

    // Initialize settings
    public function settings_init() {
        register_setting(
        'immod_instant_maintenance_mode', 
        'immod_settings'
        );


        add_settings_section(
            'immod_section',
            __('Maintenance Mode Settings', 'instant-maintenance-mode'),
            array($this, 'section_callback'),
            'immod_instant_maintenance_mode'
        );

        add_settings_field(
            'immod_enable',
            __('Enable Maintenance Mode', 'instant-maintenance-mode'),
            array($this, 'enable_callback'),
            'immod_instant_maintenance_mode',
            'immod_section'
        );

        add_settings_field(
            'immod_logo',
            __('Logo (URL)', 'instant-maintenance-mode'),
            array($this, 'logo_callback'),
            'immod_instant_maintenance_mode',
            'immod_section'
        );

        add_settings_field(
            'immod_message',
            __('Custom Message', 'instant-maintenance-mode'),
            array($this, 'message_callback'),
            'immod_instant_maintenance_mode',
            'immod_section'
        );


    }

    // Section callback
    public function section_callback() {
        echo esc_html__('Configure your maintenance mode settings below.', 'instant-maintenance-mode');
    }

    // Enable checkbox callback
    public function enable_callback() {
        $options = get_option('immod_settings');
        ?>
        <label class="immod-toggle-switch">
            <input type="checkbox" name="immod_settings[enable]" <?php checked(isset($options['enable']) && $options['enable']); ?> value="1">
            <span class="immod-toggle-slider"></span>
        </label>
        <?php
    }

    // Logo URL callback
    public function logo_callback() {
        $options = get_option('immod_settings');
        $logo_url = isset($options['logo']) ? $options['logo'] : '';
        ?>
        <input type="text" name="immod_settings[logo]" value="<?php echo esc_url($logo_url); ?>" class="regular-text">
        <p class="description"><?php esc_html__('Enter the full URL of your logo image. Leave blank to use site name.', 'instant-maintenance-mode'); ?></p>
        <?php
    }

    // Message textarea callback
    public function message_callback() {
        $options = get_option('immod_settings');
        $message = isset($options['message']) ? $options['message'] : __('Website under maintenance. Please check back soon.', 'instant-maintenance-mode');
        ?>
        <textarea name="immod_settings[message]" rows="5" cols="50" class="immod-textarea"><?php echo esc_textarea($message); ?></textarea>
        <?php
    }

    // Settings page
    public function settings_page() {
        ?>
        <div class="wrap immod-settings-wrap">
            <h1><?php esc_html__('Instant Maintenance Mode', 'instant-maintenance-mode'); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('immod_instant_maintenance_mode');
                do_settings_sections('immod_instant_maintenance_mode');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Check and display maintenance mode
    public function check_maintenance_mode() {
        $options = get_option('immod_settings');
        
        if (isset($options['enable']) && $options['enable'] && !current_user_can('manage_options')) {
            // Check if we're in admin or doing AJAX
            if (is_admin() || wp_doing_ajax()) {
                return;
            }
            
            // Check for REST API requests
            if (defined('REST_REQUEST') && REST_REQUEST) {
                return;
            }
            
            // Check for XML-RPC requests
            if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
                return;
            }
            
            // Show maintenance page
            immod_maintenance_page();
        }
    }

    // Activation hook - set default options
    public static function activate() {
        $default_options = array(
            'enable' => 0,
            'logo' => '',
            'message' => __('Website under maintenance. Please check back soon.', 'instant-maintenance-mode')
        );
        
        if (false === get_option('immod_settings')) {
            add_option('immod_settings', $default_options);
        }
    }

    // Deactivation hook - clean up
    public static function deactivate() {
        $options = get_option('immod_settings');
        if (isset($options['enable']) && $options['enable']) {
            $options['enable'] = 0;
            update_option('immod_settings', $options);
        }
    }
}