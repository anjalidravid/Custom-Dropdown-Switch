<?php
/*
Plugin Name: Custom Dropdown Switch
Plugin URI: https://example.com
Description: A plugin to create a custom dropdown switch for toggling between two URLs.
Version: 1.0
Author: Anjali dravid
Author URI: https://example.com
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function dropdown_switch_enqueue_styles() {
    wp_enqueue_style('dropdown-switch-styles', plugin_dir_url(__FILE__) . 'styles.css');
}
add_action('wp_enqueue_scripts', 'dropdown_switch_enqueue_styles');


/**
 * Register settings page
 */
add_action('admin_menu', function() {
    add_options_page(
        'Dropdown Switch Settings', 
        'Dropdown Switch', 
        'manage_options', 
        'dropdown-switch', 
        'render_dropdown_switch_settings'
    );
});

/**
 * Register settings
 */
add_action('admin_init', function() {
    register_setting('dropdown_switch_settings', 'dropdown_switch_urls');
});

/**
 * Render settings page
 */
function render_dropdown_switch_settings() {
    $urls = get_option('dropdown_switch_urls', []);
    ?>
    <div class="wrap">
        <h1>Dropdown Switch Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('dropdown_switch_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="title1">Page 1 Title</label></th>
                    <td><input type="text" id="title1" name="dropdown_switch_urls[title1]" value="<?php echo esc_attr($urls['title1'] ?? ''); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="url1">Page 1 URL</label></th>
                    <td><input type="url" id="url1" name="dropdown_switch_urls[url1]" value="<?php echo esc_url($urls['url1'] ?? ''); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="title2">Page 2 Title</label></th>
                    <td><input type="text" id="title2" name="dropdown_switch_urls[title2]" value="<?php echo esc_attr($urls['title2'] ?? ''); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="url2">Page 2 URL</label></th>
                    <td><input type="url" id="url2" name="dropdown_switch_urls[url2]" value="<?php echo esc_url($urls['url2'] ?? ''); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Register the shortcode
 */
add_shortcode('dropdown_switch', function() {
    $urls = get_option('dropdown_switch_urls', []);
    ob_start();
    ?>
    <div id="dropdown-switch">
        <button id="switch-now">Switch Now</button>
        <ul id="dropdown-options" style="display:none;">
            <li data-value="any-other">Any Other</li>
            <li data-value="page1" data-url="<?php echo esc_url($urls['url1'] ?? ''); ?>">
                <?php echo esc_html($urls['title1'] ?? 'Page 1'); ?>
            </li>
            <li data-value="page2" data-url="<?php echo esc_url($urls['url2'] ?? ''); ?>">
                <?php echo esc_html($urls['title2'] ?? 'Page 2'); ?>
            </li>
        </ul>
    </div>
    <script>
        document.getElementById('switch-now').addEventListener('click', function() {
            var dropdown = document.getElementById('dropdown-options');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        });
        document.getElementById('dropdown-options').addEventListener('click', function(e) {
            var url = e.target.getAttribute('data-url');
            if (url) window.location.href = url;
        });
        
        // Set default selection based on current URL
        document.addEventListener('DOMContentLoaded', function() {
            var currentUrl = window.location.href;
            var dropdownItems = document.querySelectorAll('#dropdown-options li');
            dropdownItems.forEach(function(item) {
                item.classList.remove('active');
                if (item.dataset.url === currentUrl) {
                    item.classList.add('active');
                }
            });
        });
    </script>
 
    <?php
    return ob_get_clean();
});
