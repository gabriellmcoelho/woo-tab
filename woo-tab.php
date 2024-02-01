<?php
/*
Plugin Name: Quick Access For Woocommerce
Plugin URI: https://github.com/gabriellmcoelho/woo-tab
Description: This plugin adds woocommerce and POS shortcuts
Version: 1.2.1
License: GPLv2 or later
Author: Gabriel Coelho
Author URI: https://gabriellmcoelho.space/
*/


// WordPressメニューに設定ページを追加
function woo_add_plugin_menu() {
    add_menu_page(
        'Quick Accessの設定', // ページのタイトル
        'Quick Access', // メニューのテキスト
        'manage_options', // アクセス権限
        'quick_access_settings', // ページのスラッグ
        'quick_access_settings_page' // ページを描画するコールバック関数
    );
}
add_action('admin_menu', 'woo_add_plugin_menu');

// 設定ページを描画する関数
function quick_access_settings_page() {
    ?>
    <div class="wrap">
        <h1>Quick Accessの設定</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('quick_access_settings_group');
            do_settings_sections('quick_access_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// 設定およびフィールドを登録する関数
function woo_register_settings() {
    register_setting('quick_access_settings_group', 'woo_enable_pedidos');
    register_setting('quick_access_settings_group', 'woo_enable_produtos');
    register_setting('quick_access_settings_group', 'woo_enable_cupons');
    register_setting('quick_access_settings_group', 'woo_enable_pos');
    register_setting('quick_access_settings_group', 'woo_enable_envios');

    add_settings_section('quick_access_section', 'ショートカットオプション', 'quick_access_section_callback', 'quick_access_settings');

    add_settings_field('woo_enable_pedidos', '注文のショートカットを有効にする', 'quick_access_checkbox_callback', 'quick_access_settings', 'quick_access_section', array('label_for' => 'woo_enable_pedidos'));
    add_settings_field('woo_enable_produtos', '製品のショートカットを有効にする', 'quick_access_checkbox_callback', 'quick_access_settings', 'quick_access_section', array('label_for' => 'woo_enable_produtos'));
    add_settings_field('woo_enable_cupons', 'クーポンのショートカットを有効にする', 'quick_access_checkbox_callback', 'quick_access_settings', 'quick_access_section', array('label_for' => 'woo_enable_cupons'));
    add_settings_field('woo_enable_pos', 'POSのショートカットを有効にする', 'quick_access_checkbox_callback', 'quick_access_settings', 'quick_access_section', array('label_for' => 'woo_enable_pos'));
    add_settings_field('woo_enable_envios', '配送のショートカットを有効にする', 'quick_access_checkbox_callback', 'quick_access_settings', 'quick_access_section', array('label_for' => 'woo_enable_envios'));
}
add_action('admin_init', 'woo_register_settings');

// セクションのコールバック
function quick_access_section_callback() {
    echo '<p>有効にするショートカットを選択してください：</p>';
}

// チェックボックスのフィールドのコールバック
function quick_access_checkbox_callback($args) {
    $option_name = $args['label_for'];
    $option_value = get_option($option_name);
    ?>
    <input type="checkbox" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($option_name); ?>" <?php checked(1, $option_value, true); ?> />
    <label for="<?php echo esc_attr($args['label_for']); ?>">有効にする</label>
    <?php
}

// WordPressメニューにショートカットを追加する関数
function woo_tabs($wp_admin_bar) {

    if (current_user_can('manage_options')) {

        // 各ショートカットのオプションを確認
        $enable_pedidos = get_option('woo_enable_pedidos', 1); // デフォルト値：1（有効）
        $enable_produtos = get_option('woo_enable_produtos', 1);
        $enable_cupons = get_option('woo_enable_cupons', 1);
        $enable_pos = get_option('woo_enable_pos', 1);
        $enable_envios = get_option('woo_enable_envios', 1);

        // サイトのベースURLを取得する
        $site_url = home_url();

        // デフォルトのショートカットを削除する
        $wp_admin_bar->remove_node('new-content'); // "新規"（新しいコンテンツを追加）を削除
        $wp_admin_bar->remove_node('comments'); // "コメント"を削除
        $wp_admin_bar->remove_node('trp_edit_translation');

        // 有効な場合、"注文"ショートカットを追加
        if ($enable_pedidos) {
            $wp_admin_bar->add_node(array(
                'id'    => 'woo-tab-pedidos',
                'title' => '注文',
                'href'  => $site_url . '/wp-admin/edit.php?post_type=shop_order',
                'meta'  => array('target' => '_blank'), // リンクを新しいタブで開く
            ));
        }

        // 有効な場合、"製品"ショートカットを追加
        if ($enable_produtos) {
            $wp_admin_bar->add_node(array(
                'id'    => 'woo-tab-produtos',
                'title' => '製品',
                'href'  => $site_url . '/wp-admin/edit.php?post_type=product',
                'meta'  => array('target' => '_blank'),
            ));
        }

        // 有効な場合、"クーポン"ショートカットを追加
        if ($enable_cupons) {
            $wp_admin_bar->add_node(array(
                'id'    => 'woo-tab-cupons',
                'title' => 'クーポン',
                'href'  => $site_url . '/wp-admin/edit.php?post_type=shop_coupon',
                'meta'  => array('target' => '_blank'),
            ));
        }

        // 有効な場合、"POS"ショートカットを追加
        if ($enable_pos) {
            $wp_admin_bar->add_node(array(
                'id'    => 'woo-tab-pos',
                'title' => 'POS',
                'href'  => $site_url . '/wp-content/plugins/woocommerce-openpos/pos/',
                'meta'  => array('target' => '_blank'),
            ));
        }

        // 有効な場合、"配送"ショートカットを追加
        if ($enable_envios) {
            $wp_admin_bar->add_node(array(
                'id'    => 'woo-tab-envios',
                'title' => '配送',
                'href'  => $site_url . '/wp-admin/admin.php?page=melhor-envio#/pedidos',
                'meta'  => array('target' => '_blank'),
            ));
        }
    }
}
add_action('admin_bar_menu', 'woo_tabs', 999);