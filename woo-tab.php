<?php
/*
Plugin Name: Quick Access For Woocommerce
Plugin URI: https://github.com/gabriellmcoelho/woo-tab
Description: This plugin adds WooCommerce and POS shortcuts
Version: 1.2.2
License: GPLv2 or later
Author: Gabriel Coelho
Author URI: https://gabriellmcoelho.space/
*/

// WordPress menu setup
function woo_add_plugin_menu() {
    add_menu_page(
        'Quick Access Settings',
        'Quick Access',
        'manage_options',
        'quick_access_settings',
        'quick_access_settings_page'
    );
}
add_action('admin_menu', 'woo_add_plugin_menu');

// Settings page rendering function
function quick_access_settings_page() {
    ?>
    <div class="wrap">
        <h1>Quick Access Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('quick_access_settings_group');
            do_settings_sections('quick_access_settings');
            submit_button();
            ?>
        </form>
    </div>
    <script>
        // Script to check checkboxes based on saved options
        document.addEventListener('DOMContentLoaded', function () {
            var checkboxes = document.querySelectorAll('.checkbox-enable');
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = checkbox.value === '1';
            });
        });
    </script>
    <?php
}

// Register settings and fields
function woo_register_settings() {
    register_setting('quick_access_settings_group', 'woo_enable_pedidos');
    register_setting('quick_access_settings_group', 'woo_enable_produtos');
    register_setting('quick_access_settings_group', 'woo_enable_cupons');
    register_setting('quick_access_settings_group', 'woo_enable_pos');
    register_setting('quick_access_settings_group', 'woo_enable_envios');

    add_settings_section('quick_access_section', 'Shortcut Options', 'quick_access_section_callback', 'quick_access_settings');

    add_settings_field('woo_enable_pedidos', 'Enable Orders Shortcut', 'quick_access_checkbox_callback', 'quick_access_settings', 'quick_access_section', array('label_for' => 'woo_enable_pedidos'));
    add_settings_field('woo_enable_produtos', 'Enable Products Shortcut', 'quick_access_checkbox_callback', 'quick_access_settings', 'quick_access_section', array('label_for' => 'woo_enable_produtos'));
    add_settings_field('woo_enable_cupons', 'Enable Coupons Shortcut', 'quick_access_checkbox_callback', 'quick_access_settings', 'quick_access_section', array('label_for' => 'woo_enable_cupons'));
    add_settings_field('woo_enable_pos', 'Enable POS Shortcut', 'quick_access_checkbox_callback', 'quick_access_settings', 'quick_access_section', array('label_for' => 'woo_enable_pos'));
    add_settings_field('woo_enable_envios', 'Enable Shipping Shortcut', 'quick_access_checkbox_callback', 'quick_access_settings', 'quick_access_section', array('label_for' => 'woo_enable_envios'));
}
add_action('admin_init', 'woo_register_settings');

// Section callback
function quick_access_section_callback() {
    echo '<p>Select the shortcuts to enable:</p>';
}

// Checkbox field callback with a class for easier selection
function quick_access_checkbox_callback($args) {
    $option_name = $args['label_for'];
    $option_value = get_option($option_name, 0); // Set default value to 0 if not exists

    ?>
    <input type="checkbox" class="checkbox-enable" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($option_name); ?>" <?php checked(1, $option_value, true); ?> value="1" />
    <label for="<?php echo esc_attr($args['label_for']); ?>">Enable</label>
    <?php
}

// Add shortcuts to the WordPress menu
function woo_tabs($wp_admin_bar) {
    if (current_user_can('manage_options')) {
        // Check options for each shortcut
        $enable_pedidos = get_option('woo_enable_pedidos', 1); // Default value: 1 (enabled)
        $enable_produtos = get_option('woo_enable_produtos', 1);
        $enable_cupons = get_option('woo_enable_cupons', 1);
        $enable_pos = get_option('woo_enable_pos', 1);
        $enable_envios = get_option('woo_enable_envios', 1);

        // Get the base URL of the site
        $site_url = home_url();

        // Remove default shortcuts
        $wp_admin_bar->remove_node('new-content'); // Remove "New" (Add new content)
        $wp_admin_bar->remove_node('comments'); // Remove "Comments"
        $wp_admin_bar->remove_node('trp_edit_translation');

        // Add "Orders" shortcut if enabled
        if ($enable_pedidos) {
            $wp_admin_bar->add_node(array(
                'id'    => 'woo-tab-pedidos',
                'title' => 'Orders',
                'href'  => $site_url . '/wp-admin/edit.php?post_type=shop_order',
                'meta'  => array('target' => '_blank'), // Open link in a new tab
            ));
        }

        // Add "Products" shortcut if enabled
        if ($enable_produtos) {
            $wp_admin_bar->add_node(array(
                'id'    => 'woo-tab-produtos',
                'title' => 'Products',
                'href'  => $site_url . '/wp-admin/edit.php?post_type=product',
                'meta'  => array('target' => '_blank'),
            ));
        }

        // Add "Coupons" shortcut if enabled
        if ($enable_cupons) {
            $wp_admin_bar->add_node(array(
                'id'    => 'woo-tab-cupons',
                'title' => 'Coupons',
                'href'  => $site_url . '/wp-admin/edit.php?post_type=shop_coupon',
                'meta'  => array('target' => '_blank'),
            ));
        }

        // Add "POS" shortcut if enabled
        if ($enable_pos) {
            $wp_admin_bar->add_node(array(
                'id'    => 'woo-tab-pos',
                'title' => 'POS',
                'href'  => $site_url . '/wp-content/plugins/woocommerce-openpos/pos/',
                'meta'  => array('target' => '_blank'),
            ));
        }

        // Add "Shipping" shortcut if enabled
        if ($enable_envios) {
            $wp_admin_bar->add_node(array(
                'id'    => 'woo-tab-envios',
                'title' => 'Shipping',
                'href'  => $site_url . '/wp-admin/admin.php?page=melhor-envio#/pedidos',
                'meta'  => array('target' => '_blank'),
            ));
        }
    }
}
add_action('admin_bar_menu', 'woo_tabs', 999);